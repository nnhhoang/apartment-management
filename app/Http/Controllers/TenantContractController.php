<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantContractRequest;
use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\Tenant;
use App\Models\TenantContract;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TenantContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = TenantContract::whereHas('room.apartment', function($q) {
            $q->where('user_id', Auth::id());
        });
        
        // Lọc theo trạng thái hợp đồng
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->whereNull('end_date');
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('end_date');
            }
        }
        
        // Lọc theo tòa nhà
        if ($request->has('apartment_id') && $request->apartment_id) {
            $query->whereHas('room', function($q) use ($request) {
                $q->where('apartment_id', $request->apartment_id);
            });
        }
        
        // Lọc theo phòng
        if ($request->has('room_id') && $request->room_id) {
            $query->where('apartment_room_id', $request->room_id);
        }
        
        // Lọc theo người thuê
        if ($request->has('tenant_id') && $request->tenant_id) {
            $query->where('tenant_id', $request->tenant_id);
        }
        
        // Phân trang kết quả
        $contracts = $query->with(['room.apartment', 'tenant'])
            ->orderBy('start_date', 'desc')
            ->paginate(10);
        
        // Lấy danh sách tòa nhà để dropdown filter
        $apartments = Apartment::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('tenant_contracts.index', compact('contracts', 'apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        // Lấy danh sách phòng trọ chưa có người thuê
        $rooms = ApartmentRoom::whereHas('apartment', function($q) {
            $q->where('user_id', Auth::id());
        })
        ->whereDoesntHave('contracts', function($q) {
            $q->whereNull('end_date'); // Không có hợp đồng đang active
        })
        ->with('apartment')
        ->get();
        
        // Nếu có room_id từ query string
        $selectedRoomId = $request->room_id;
        
        return view('tenant_contracts.create', compact('rooms', 'selectedRoomId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantContractRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Kiểm tra quyền truy cập phòng
        $room = ApartmentRoom::findOrFail($validatedData['apartment_room_id']);
        if (Gate::denies('view-room', $room)) {
            abort(403, 'Unauthorized action.');
        }
        
        // Kiểm tra xem phòng đã có người thuê chưa
        if ($room->activeContract()->exists()) {
            return back()->with('error', 'Phòng này đã có người thuê.');
        }
        
        // Nếu là tenant_id mới (không tồn tại trong database), tạo mới tenant
        if ($request->has('new_tenant') && $request->new_tenant) {
            $tenant = Tenant::create([
                'name' => $request->tenant_name,
                'tel' => $request->tenant_tel,
                'email' => $request->tenant_email,
                'identity_card_number' => $request->tenant_identity_card_number,
            ]);
            
            $validatedData['tenant_id'] = $tenant->id;
        }
        
        // Format ngày
        $validatedData['start_date'] = Carbon::parse($validatedData['start_date']);
        if (!empty($validatedData['end_date'])) {
            $validatedData['end_date'] = Carbon::parse($validatedData['end_date']);
        }
        
        // Tạo hợp đồng
        $contract = TenantContract::create($validatedData);
        
        return redirect()->route('tenant_contracts.show', $contract)
            ->with('success', 'Hợp đồng đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TenantContract $tenantContract): View
    {
        if (Gate::denies('view-contract', $tenantContract)) {
            abort(403, 'Unauthorized action.');
        }
        
        $tenantContract->load(['room.apartment', 'tenant', 'feeCollections' => function($query) {
            $query->orderBy('charge_date', 'desc');
        }]);
        
        return view('tenant_contracts.show', compact('tenantContract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TenantContract $tenantContract): View
    {
        if (Gate::denies('update-contract', $tenantContract)) {
            abort(403, 'Unauthorized action.');
        }
        
        $tenantContract->load(['room.apartment', 'tenant']);
        
        return view('tenant_contracts.edit', compact('tenantContract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantContractRequest $request, TenantContract $tenantContract): RedirectResponse
    {
        if (Gate::denies('update-contract', $tenantContract)) {
            abort(403, 'Unauthorized action.');
        }
        
        $validatedData = $request->validated();
        
        // Format ngày
        $validatedData['start_date'] = Carbon::parse($validatedData['start_date']);
        if (!empty($validatedData['end_date'])) {
            $validatedData['end_date'] = Carbon::parse($validatedData['end_date']);
        }
        
        $tenantContract->update($validatedData);
        
        return redirect()->route('tenant_contracts.show', $tenantContract)
            ->with('success', 'Hợp đồng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TenantContract $tenantContract): RedirectResponse
    {
        if (Gate::denies('update-contract', $tenantContract)) {
            abort(403, 'Unauthorized action.');
        }
        
        // Kiểm tra xem hợp đồng đã có khoản thu tiền chưa
        if ($tenantContract->feeCollections()->exists()) {
            return back()->with('error', 'Không thể xóa hợp đồng đã có khoản thu tiền.');
        }
        
        $tenantContract->delete();
        
        return redirect()->route('tenant_contracts.index')
            ->with('success', 'Hợp đồng đã được xóa thành công.');
    }
    
    /**
     * Kết thúc hợp đồng
     */
    public function endContract(Request $request, TenantContract $tenantContract): RedirectResponse
    {
        if (Gate::denies('update-contract', $tenantContract)) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'end_date' => 'required|date',
        ]);
        
        $tenantContract->end_date = Carbon::parse($request->end_date);
        $tenantContract->save();
        
        return redirect()->route('tenant_contracts.show', $tenantContract)
            ->with('success', 'Hợp đồng đã được kết thúc thành công.');
    }
}