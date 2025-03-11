<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Get tenant list that belongs to the current user's apartments or have no contracts
        $query = Tenant::where(function($q) {
            $q->whereHas('contracts.room.apartment', function($subQ) {
                $subQ->where('user_id', Auth::id());
            })
            ->orWhereDoesntHave('contracts');
        });
        
        // Search by name, email, phone or ID card number
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('tel', 'like', "%{$search}%")
                  ->orWhere('identity_card_number', 'like', "%{$search}%");
            });
        }
        
        // Paginate results
        $tenants = $query->orderBy('name')->paginate(10);
        
        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        // Check if we're coming from contract creation
        $returnToContract = $request->has('return_to') && $request->return_to === 'contract';
        $roomId = $request->has('room_id') ? $request->room_id : null;
        
        return view('tenants.create', compact('returnToContract', 'roomId'));
    }
    
    public function store(TenantRequest $request): RedirectResponse|JsonResponse
    {
        $validatedData = $request->validated();
        
        $tenant = Tenant::create($validatedData);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tenant' => $tenant,
                'message' => 'Người thuê đã được tạo thành công.'
            ]);
        }
        
        // Check if we should return to contract creation
        if ($request->has('return_to') && $request->return_to === 'contract') {
            $roomId = $request->has('room_id') ? "?room_id={$request->room_id}&" : "?";
            return redirect()->route('tenant_contracts.create', $roomId . 'selected_tenant=' . $tenant->id)
                ->with('success', 'Người thuê đã được tạo thành công. Hãy tiếp tục tạo hợp đồng.');
        }
        
        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Người thuê đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);
        
        $tenant->load(['contracts.room.apartment', 'feeCollections' => function($query) {
            $query->orderBy('charge_date', 'desc');
        }]);
        
        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant): View
    {
        $this->authorize('update', $tenant);
        
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $this->authorize('update', $tenant);
        
        $validatedData = $request->validated();
        
        $tenant->update($validatedData);
        
        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Thông tin người thuê đã được cập nhật.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->authorize('delete', $tenant);

        if ($tenant->contracts()->exists()) {
            return back()->with('error', 'Không thể xóa người thuê đã có hợp đồng.');
        }
        
        $tenant->delete();
        
        return redirect()->route('tenants.index')
            ->with('success', 'Người thuê đã được xóa thành công.');
    }
    
    /**
     * API to get tenant list for dropdown
     */
    public function getTenants(Request $request): JsonResponse
    {
        // Get tenants who belong to this user and tenants with no contracts
        $query = Tenant::where(function($q) {
            $q->whereHas('contracts.room.apartment', function($subQ) {
                $subQ->where('user_id', Auth::id());
            });
            
            // Or tenants with no contracts
            $q->orWhereDoesntHave('contracts');
        });
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('tel', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('identity_card_number', 'like', "%{$search}%");
            });
        }
        
        $tenants = $query->orderBy('name')->limit(20)->get();
        
        return response()->json($tenants);
    }
}