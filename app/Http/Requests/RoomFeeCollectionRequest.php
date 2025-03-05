<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomFeeCollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'tenant_contract_id' => 'required|exists:tenant_contracts,id',
            'electricity_number_before' => 'required|integer|min:0',
            'electricity_number_after' => 'required|integer|gte:electricity_number_before',
            'water_number_before' => 'required|integer|min:0',
            'water_number_after' => 'required|integer|gte:water_number_before',
            'charge_date' => 'required|date',
            'total_paid' => 'required|numeric|min:0',
            'electricity_image' => 'nullable|image|max:2048',
            'water_image' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'electricity_number_before.required' => 'Số điện sử dụng ban đầu là bắt buộc',
            'electricity_number_after.required' => 'Số điện sử dụng cuối kỳ là bắt buộc',
            'water_number_before.required' => 'Số nước sử dụng ban đầu là bắt buộc',
            'water_number_after.required' => 'Số nước sử dụng cuối kỳ là bắt buộc',
            'total_paid.required' => 'Tổng số tiền đã thanh toán là bắt buộc',
        ];
    }
}