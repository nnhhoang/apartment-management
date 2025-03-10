<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantContractRequest extends FormRequest
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
        // Nếu đang tạo tenant mới, không bắt buộc phải có tenant_id
        $tenant_id_rule = 'required|exists:tenants,id';
        if ($this->has('new_tenant') && $this->new_tenant == "1") {
            $tenant_id_rule = 'nullable';
        }

        return [
            'apartment_room_id' => 'required|exists:apartment_rooms,id',
            'tenant_id' => $tenant_id_rule,
            'pay_period' => 'required|integer|in:1,3,6,12',
            'price' => 'required|numeric|min:0',
            'electricity_pay_type' => 'required|integer|in:1,2,3',
            'electricity_price' => 'required|numeric|min:0',
            'electricity_number_start' => 'required|integer|min:0',
            'water_pay_type' => 'required|integer|in:1,2,3',
            'water_price' => 'required|numeric|min:0',
            'water_number_start' => 'required|integer|min:0',
            'number_of_tenant_current' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }
}