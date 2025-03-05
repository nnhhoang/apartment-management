<?php

namespace App\Http\Controllers;

use App\Models\TenantContract;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ContractAIController extends Controller
{
    /**
     * Service OpenAI.
     */
    protected OpenAIService $openAIService;

    /**
     * Khởi tạo controller với service OpenAI.
     */
    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Hiển thị giao diện trợ lý AI cho hợp đồng.
     */
    public function show(TenantContract $tenantContract): View
    {
        // Kiểm tra quyền truy cập
        $this->authorize('view', $tenantContract->room->apartment);
        
        // Load các mối quan hệ cần thiết
        $tenantContract->load(['room.apartment', 'tenant']);
        
        return view('tenant_contracts.ai_assistant', compact('tenantContract'));
    }

    /**
     * Xử lý câu hỏi về hợp đồng.
     */
    public function askQuestion(Request $request, TenantContract $tenantContract): JsonResponse
    {
        // Kiểm tra quyền truy cập
        $this->authorize('view', $tenantContract->room->apartment);
        
        // Validate câu hỏi
        $validated = $request->validate([
            'question' => 'required|string|min:5|max:500',
        ]);
        
        // Load các mối quan hệ cần thiết
        $tenantContract->load(['room.apartment', 'tenant']);
        
        // Chuẩn bị dữ liệu hợp đồng để gửi đến AI
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
        
        // Gửi câu hỏi đến AI
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

    /**
     * Hiển thị trang trợ lý AI cho người thuê.
     */
    public function tenantAssistant(): View
    {
        // Lấy hợp đồng đang hoạt động của người dùng
        $user = Auth::user();
        
        // Tìm các hợp đồng mà người dùng là người thuê
        $activeContracts = TenantContract::whereHas('tenant', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->whereNull('end_date')
            ->with(['room.apartment', 'tenant'])
            ->get();
            
        return view('tenant_contracts.tenant_ai_assistant', compact('activeContracts'));
    }
}