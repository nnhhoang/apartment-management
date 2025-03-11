<?php

namespace App\Http\Controllers;

use App\Models\TenantContract;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ContractAIController extends Controller
{
    /**
     * Service OpenAI.
     */
    protected OpenAIService $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }


    public function show(TenantContract $tenantContract): View
    {

        if (Gate::denies('view-contract', $tenantContract)) {
            abort(403, 'Unauthorized action.');
        }

        $tenantContract->load(['room.apartment', 'tenant']);
        
        return view('tenant_contracts.ai_assistant', compact('tenantContract'));
    }

    public function askQuestion(Request $request, TenantContract $tenantContract): JsonResponse
    {
        if (Gate::denies('view-contract', $tenantContract)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'question' => 'required|string|min:5|max:500',
        ]);

        $tenantContract->load(['room.apartment', 'tenant']);

        $contractData = [
            'tenant_name' => $tenantContract->tenant->name,
            'room_number' => $tenantContract->room->room_number,
            'apartment_name' => $tenantContract->room->apartment->name,
            'apartment_address' => $tenantContract->room->apartment->address,
            'price' => $tenantContract->price,
            'pay_period' => $tenantContract->pay_period,
            'electricity_pay_type' => $tenantContract->electricity_pay_type,
            'electricity_price' => $tenantContract->electricity_price,
            'electricity_number_start' => $tenantContract->electricity_number_start,
            'water_pay_type' => $tenantContract->water_pay_type,
            'water_price' => $tenantContract->water_price,
            'water_number_start' => $tenantContract->water_number_start,
            'number_of_tenant_current' => $tenantContract->number_of_tenant_current,
            'start_date' => $tenantContract->start_date->format('d/m/Y'),
            'end_date' => $tenantContract->end_date ? $tenantContract->end_date->format('d/m/Y') : null,
            'note' => $tenantContract->note,
        ];

        $answer = $this->openAIService->askAboutContract($validated['question'], $contractData);
        
        if ($answer) {
            return response()->json([
                'success' => true,
                'answer' => $answer,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối với dịch vụ AI. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    public function tenantAssistant(): View
    {

        $user = Auth::user();

        $activeContracts = TenantContract::whereHas('tenant', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->whereNull('end_date')
            ->with(['room.apartment', 'tenant'])
            ->get();
            
        return view('tenant_contracts.tenant_ai_assistant', compact('activeContracts'));
    }
}