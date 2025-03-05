<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    /**
     * API key của OpenAI.
     */
    protected string $apiKey;

    /**
     * URL API của OpenAI.
     */
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    /**
     * Model GPT sử dụng.
     */
    protected string $model = 'gpt-4o';

    /**
     * Khởi tạo service OpenAI.
     */
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Gửi yêu cầu đến API OpenAI.
     *
     * @param string $prompt Nội dung câu hỏi
     * @param array $contextData Dữ liệu ngữ cảnh (thông tin hợp đồng)
     * @return string|null Phản hồi từ GPT
     */
    public function askAboutContract(string $prompt, array $contextData): ?string
    {
        try {
            // Tạo nội dung hệ thống (system content) với ngữ cảnh về hợp đồng
            $systemContent = $this->buildSystemContent($contextData);
            
            // Thực hiện gọi API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemContent
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['choices'][0]['message']['content'] ?? null;
            } else {
                Log::error('OpenAI API error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('OpenAI service error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Xây dựng nội dung system cho AI dựa trên dữ liệu hợp đồng.
     *
     * @param array $contractData Dữ liệu hợp đồng
     * @return string Nội dung system prompt
     */
    protected function buildSystemContent(array $contractData): string
    {
        // Xử lý loại thanh toán
        $electricityPayType = match ($contractData['electricity_pay_type']) {
            1 => 'theo đầu người',
            2 => 'cố định theo phòng',
            3 => 'theo số điện sử dụng',
            default => 'không xác định'
        };

        $waterPayType = match ($contractData['water_pay_type']) {
            1 => 'theo đầu người',
            2 => 'cố định theo phòng',
            3 => 'theo số nước sử dụng',
            default => 'không xác định'
        };

        $payPeriod = match ($contractData['pay_period']) {
            1 => 'hàng tháng',
            3 => '3 tháng một lần',
            6 => '6 tháng một lần',
            12 => 'hàng năm',
            default => 'không xác định'
        };

        // Định dạng số tiền
        $formatMoney = function($amount) {
            return number_format($amount, 0, ',', '.') . ' VNĐ';
        };
        
        // Chuẩn bị nội dung end_date
        $endDate = isset($contractData['end_date']) ? $contractData['end_date'] : 'Chưa kết thúc';
        
        // Chuẩn bị nội dung ghi chú
        $note = empty($contractData['note']) ? 'Không có ghi chú' : $contractData['note'];

        // Tạo prompt cho system
        return <<<EOT
Bạn là trợ lý AI chuyên về giải đáp thắc mắc liên quan đến hợp đồng thuê trọ. Dưới đây là thông tin về hợp đồng thuê trọ bạn đang xem xét:

THÔNG TIN HỢP ĐỒNG:
- Người thuê: {$contractData['tenant_name']}
- Phòng số: {$contractData['room_number']}
- Tòa nhà: {$contractData['apartment_name']}
- Địa chỉ: {$contractData['apartment_address']}
- Giá thuê: {$formatMoney($contractData['price'])}
- Kỳ hạn thanh toán: {$payPeriod}
- Ngày bắt đầu: {$contractData['start_date']}
- Ngày kết thúc: {$endDate}
- Số người ở hiện tại: {$contractData['number_of_tenant_current']}

CHI TIẾT THANH TOÁN:
- Tiền điện: {$formatMoney($contractData['electricity_price'])} ({$electricityPayType})
- Chỉ số điện ban đầu: {$contractData['electricity_number_start']}
- Tiền nước: {$formatMoney($contractData['water_price'])} ({$waterPayType})
- Chỉ số nước ban đầu: {$contractData['water_number_start']}

GHI CHÚ:
{$note}

Hãy trả lời câu hỏi của người dùng dựa trên thông tin hợp đồng trên. Nếu không có thông tin để trả lời chính xác, hãy nói rằng bạn không có đủ thông tin và khuyên họ liên hệ với chủ nhà. Câu trả lời phải ngắn gọn, dễ hiểu và chính xác.
EOT;
    }
}