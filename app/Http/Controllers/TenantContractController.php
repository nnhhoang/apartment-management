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
use Illuminate\Support\Facades\DB;
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
        
        // Lọc theo ngày bắt đầu
        if ($request->has('start_date') && $request->start_date) {
            $startDate = Carbon::parse($request->start_date);
            $query->whereDate('start_date', '>=', $startDate);
        }
        
        // Lọc theo khoảng thời gian
        if ($request->has('date_range') && $request->date_range) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $startDate = Carbon::parse($dates[0]);
                $endDate = Carbon::parse($dates[1]);
                $query->whereBetween('start_date', [$startDate, $endDate]);
            }
        }
        
        // Phân trang kết quả
        $contracts = $query->with(['room.apartment', 'tenant', 'feeCollections'])
            ->orderBy('start_date', 'desc')
            ->paginate(10);
        
        // Lấy danh sách tòa nhà để dropdown filter
        $apartments = Apartment::where('user_id', Auth::id())->orderBy('name')->get();
        
        // Lấy danh sách người thuê
        $tenants = Tenant::whereHas('contracts.room.apartment', function($q) {
            $q->where('user_id', Auth::id());
        })->orderBy('name')->get();
        
        return view('tenant_contracts.index', compact('contracts', 'apartments', 'tenants'));
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
        
        // Nếu có selected_tenant từ query string (when returning from tenant creation)
        $selectedTenantId = $request->selected_tenant;
        
        // Nếu đã chọn phòng, lấy thông tin giá mặc định
        $defaultPrice = null;
        if ($selectedRoomId) {
            $room = ApartmentRoom::find($selectedRoomId);
            if ($room) {
                $defaultPrice = $room->default_price;
            }
        }
        
        // Lấy người thuê đã chọn (nếu có)
        $selectedTenant = null;
        if ($selectedTenantId) {
            $selectedTenant = Tenant::find($selectedTenantId);
        }
        
        return view('tenant_contracts.create', compact(
            'rooms', 
            'selectedRoomId', 
            'selectedTenantId', 
            'defaultPrice',
            'selectedTenant'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantContractRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validated();
            
            // Kiểm tra quyền truy cập phòng
            $room = ApartmentRoom::findOrFail($validatedData['apartment_room_id']);
            $this->authorize('view', $room);
            
            // Kiểm tra xem phòng đã có người thuê chưa
            if ($room->activeContract()->exists()) {
                return back()->with('error', 'Phòng này đã có người thuê.');
            }
            
            // Nếu là tenant_id mới (không tồn tại trong database), tạo mới tenant
            if ($request->has('new_tenant') && $request->new_tenant == "1") {
                // Kiểm tra dữ liệu tenant mới
                $request->validate([
                    'tenant_name' => 'required|string|max:45',
                    'tenant_tel' => 'required|string|max:45',
                    'tenant_email' => 'nullable|email|max:256',
                    'tenant_identity_card_number' => 'nullable|string|max:45',
                ]);
                
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
            } else {
                // Đảm bảo end_date là null khi trống
                $validatedData['end_date'] = null;
            }
            
            // Tạo hợp đồng
            $contract = TenantContract::create($validatedData);
            
            DB::commit();
            
            return redirect()->route('tenant_contracts.show', $contract)
                ->with('success', 'Hợp đồng đã được tạo thành công.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi khi tạo hợp đồng: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TenantContract $tenantContract): View
    {
        $this->authorize('view', $tenantContract);
        
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
        $this->authorize('update', $tenantContract);
        
        $tenantContract->load(['room.apartment', 'tenant']);
        
        return view('tenant_contracts.edit', compact('tenantContract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantContractRequest $request, TenantContract $tenantContract): RedirectResponse
    {
        $this->authorize('update', $tenantContract);
        
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validated();
            
            // Format ngày
            $validatedData['start_date'] = Carbon::parse($validatedData['start_date']);
            if (!empty($validatedData['end_date'])) {
                $validatedData['end_date'] = Carbon::parse($validatedData['end_date']);
            } else {
                // Đảm bảo end_date là null khi trống
                $validatedData['end_date'] = null;
            }
            
            $tenantContract->update($validatedData);
            
            DB::commit();
            
            return redirect()->route('tenant_contracts.show', $tenantContract)
                ->with('success', 'Hợp đồng đã được cập nhật thành công.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật hợp đồng: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TenantContract $tenantContract): RedirectResponse
    {
        $this->authorize('delete', $tenantContract);
        
        // Kiểm tra xem hợp đồng đã có khoản thu tiền chưa
        if ($tenantContract->feeCollections()->exists()) {
            return back()->with('error', 'Không thể xóa hợp đồng đã có khoản thu tiền.');
        }
        
        DB::beginTransaction();
        
        try {
            $tenantContract->delete();
            
            DB::commit();
            
            return redirect()->route('tenant_contracts.index')
                ->with('success', 'Hợp đồng đã được xóa thành công.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi khi xóa hợp đồng: ' . $e->getMessage());
        }
    }
    
    /**
     * Kết thúc hợp đồng
     */
    public function endContract(Request $request, TenantContract $tenantContract): RedirectResponse
    {
        $this->authorize('update', $tenantContract);
        
        $request->validate([
            'end_date' => 'required|date',
            'end_reason' => 'nullable|string|max:255',
        ]);
        
        $tenantContract->end_date = Carbon::parse($request->end_date);
        $tenantContract->note = $tenantContract->note ? $tenantContract->note . "\n\nLý do kết thúc: " . $request->end_reason : "Lý do kết thúc: " . $request->end_reason;
        $tenantContract->save();
        
        return redirect()->route('tenant_contracts.show', $tenantContract)
            ->with('success', 'Hợp đồng đã được kết thúc thành công.');
    }
}