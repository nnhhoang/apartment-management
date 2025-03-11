<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Requests\RoomFeeCollectionRequest;
use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\RoomFeeCollection;
use App\Models\RoomFeeCollectionHistory;
use App\Models\TenantContract;
use App\Services\FeeCalculationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoomFeeCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = RoomFeeCollection::whereHas('room.apartment', function($q) {
            $q->where('user_id', Auth::id());
        });

        if ($request->has('apartment_id') && $request->apartment_id) {
            $query->whereHas('room', function($q) use ($request) {
                $q->where('apartment_id', $request->apartment_id);
            });
        }

        if ($request->has('room_id') && $request->room_id) {
            $query->where('apartment_room_id', $request->room_id);
        }

        if ($request->has('tenant_id') && $request->tenant_id) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->has('month') && $request->month) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('charge_date', $date->month)
                  ->whereYear('charge_date', $date->year);
        }

        if ($request->has('payment_status') && $request->payment_status !== '') {
            if ($request->payment_status === 'unpaid') {
                $query->whereRaw('total_paid < total_price');
            } elseif ($request->payment_status === 'paid') {
                $query->whereRaw('total_paid >= total_price');
            }
        }

        $feeCollections = $query->with(['room.apartment', 'tenant', 'contract'])
            ->orderBy('charge_date', 'desc')
            ->paginate(10);

        $apartments = Apartment::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('room_fees.index', compact('feeCollections', 'apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {

        $rooms = ApartmentRoom::whereHas('apartment', function($q) {
            $q->where('user_id', Auth::id());
        })
        ->whereHas('contracts', function($q) {
            $q->whereNull('end_date'); 
        })
        ->with(['apartment', 'activeContract.tenant'])
        ->get();

        $selectedRoomId = $request->room_id;

        $selectedContractId = $request->contract_id;
        
        return view('room_fees.create', compact('rooms', 'selectedRoomId', 'selectedContractId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomFeeCollectionRequest $request, FeeCalculationService $feeService): RedirectResponse
    {
        $validatedData = $request->validated();

        $contract = TenantContract::findOrFail($validatedData['tenant_contract_id']);
        $this->authorize('view', $contract);

        if (!$contract || $contract->end_date) {
            return back()->with('error', 'Phòng này chưa có hợp đồng đang active.');
        }

        $room = $contract->room;
        $tenant = $contract->tenant;

        $calculation = $feeService->calculateFee(
            $contract,
            $validatedData['electricity_number_before'],
            $validatedData['electricity_number_after'],
            $validatedData['water_number_before'],
            $validatedData['water_number_after']
        );

        $uuid = Str::uuid()->toString();

        $feeData = [
            'tenant_contract_id' => $contract->id,
            'apartment_room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'electricity_number_before' => $validatedData['electricity_number_before'],
            'electricity_number_after' => $validatedData['electricity_number_after'],
            'water_number_before' => $validatedData['water_number_before'],
            'water_number_after' => $validatedData['water_number_after'],
            'charge_date' => Carbon::parse($validatedData['charge_date']),
            'total_debt' => 0, 
            'total_price' => $calculation['totalPrice'],
            'total_paid' => $validatedData['total_paid'],
            'fee_collection_uuid' => $uuid,
        ];
        

        if ($request->hasFile('electricity_image')) {
            $path = $request->file('electricity_image')->store('electricity_meters', 'public');
            $feeData['electricity_image'] = $path;
        }

        if ($request->hasFile('water_image')) {
            $path = $request->file('water_image')->store('water_meters', 'public');
            $feeData['water_image'] = $path;
        }

        $feeData['total_debt'] = $calculation['totalPrice'] - $validatedData['total_paid'];

        $feeCollection = RoomFeeCollection::create($feeData);

        if ($validatedData['total_paid'] > 0) {
            RoomFeeCollectionHistory::create([
                'room_fee_collection_id' => $feeCollection->id,
                'paid_date' => Carbon::now(),
                'price' => $validatedData['total_paid'],
            ]);
        }
        
        return redirect()->route('room_fees.show', $feeCollection)
            ->with('success', 'Khoản thu tiền phòng đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomFeeCollection $roomFeeCollection): View
    {
        $this->authorize('view', $roomFeeCollection);
        
        $roomFeeCollection->load(['room.apartment', 'tenant', 'contract', 'histories']);
        
        return view('room_fees.show', compact('roomFeeCollection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomFeeCollection $roomFeeCollection): View
    {
        $this->authorize('update', $roomFeeCollection);
        
        $roomFeeCollection->load(['room.apartment', 'tenant', 'contract', 'histories']);
        
        return view('room_fees.edit', compact('roomFeeCollection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomFeeCollectionRequest $request, RoomFeeCollection $roomFeeCollection, FeeCalculationService $feeService): RedirectResponse
    {
        $this->authorize('update', $roomFeeCollection);
        
        $validatedData = $request->validated();

        $contract = TenantContract::findOrFail($validatedData['tenant_contract_id']);
        $this->authorize('view', $contract);

        $calculation = $feeService->calculateFee(
            $contract,
            $validatedData['electricity_number_before'],
            $validatedData['electricity_number_after'],
            $validatedData['water_number_before'],
            $validatedData['water_number_after']
        );

        $feeData = [
            'electricity_number_before' => $validatedData['electricity_number_before'],
            'electricity_number_after' => $validatedData['electricity_number_after'],
            'water_number_before' => $validatedData['water_number_before'],
            'water_number_after' => $validatedData['water_number_after'],
            'charge_date' => Carbon::parse($validatedData['charge_date']),
            'total_price' => $calculation['totalPrice'],
            'total_paid' => $validatedData['total_paid'],
        ];

        if ($request->hasFile('electricity_image')) {

            if ($roomFeeCollection->electricity_image) {
                Storage::disk('public')->delete($roomFeeCollection->electricity_image);
            }
            
            $path = $request->file('electricity_image')->store('electricity_meters', 'public');
            $feeData['electricity_image'] = $path;
        }

        if ($request->hasFile('water_image')) {

            if ($roomFeeCollection->water_image) {
                Storage::disk('public')->delete($roomFeeCollection->water_image);
            }
            
            $path = $request->file('water_image')->store('water_meters', 'public');
            $feeData['water_image'] = $path;
        }

        $feeData['total_debt'] = $calculation['totalPrice'] - $validatedData['total_paid'];

        if ($roomFeeCollection->total_paid != $validatedData['total_paid']) {

            $roomFeeCollection->histories()->delete();

            if ($validatedData['total_paid'] > 0) {
                RoomFeeCollectionHistory::create([
                    'room_fee_collection_id' => $roomFeeCollection->id,
                    'paid_date' => Carbon::now(),
                    'price' => $validatedData['total_paid'],
                ]);
            }
        }

        $roomFeeCollection->update($feeData);
        
        return redirect()->route('room_fees.show', $roomFeeCollection)
            ->with('success', 'Khoản thu tiền phòng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomFeeCollection $roomFeeCollection): RedirectResponse
    {
        $this->authorize('delete', $roomFeeCollection);

        if ($roomFeeCollection->electricity_image) {
            Storage::disk('public')->delete($roomFeeCollection->electricity_image);
        }

        if ($roomFeeCollection->water_image) {
            Storage::disk('public')->delete($roomFeeCollection->water_image);
        }

        $roomFeeCollection->histories()->delete();

        $roomFeeCollection->delete();
        
        return redirect()->route('room_fees.index')
            ->with('success', 'Khoản thu tiền phòng đã được xóa thành công.');
    }

    public function addPayment(PaymentRequest $request, RoomFeeCollection $roomFeeCollection): RedirectResponse
    {
        $this->authorize('update', $roomFeeCollection);
        
        $validatedData = $request->validated();

        RoomFeeCollectionHistory::create([
            'room_fee_collection_id' => $roomFeeCollection->id,
            'paid_date' => Carbon::parse($validatedData['payment_date']),
            'price' => $validatedData['payment_amount'],
        ]);

        $roomFeeCollection->total_paid += $validatedData['payment_amount'];
        $roomFeeCollection->total_debt = $roomFeeCollection->total_price - $roomFeeCollection->total_paid;
        $roomFeeCollection->save();
        
        return redirect()->route('room_fees.show', $roomFeeCollection)
            ->with('success', 'Khoản thanh toán đã được thêm thành công.');
    }
}