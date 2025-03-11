<?php

namespace App\Http\Requests;

use App\Models\ApartmentRoom;
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

        $rules = [
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

        // Thêm validation cho số người ở so với giới hạn tối đa của phòng
        if ($this->has('apartment_room_id')) {
            $room = ApartmentRoom::find($this->apartment_room_id);
            if ($room && $room->max_tenant > 0) {
                $rules['number_of_tenant_current'] = 'required|integer|min:1|max:' . $room->max_tenant;
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'apartment_room_id.required' => 'Phòng trọ là bắt buộc',
            'apartment_room_id.exists' => 'Phòng trọ không tồn tại',
            'tenant_id.required' => 'Người thuê là bắt buộc',
            'tenant_id.exists' => 'Người thuê không tồn tại',
            'pay_period.required' => 'Kỳ hạn thanh toán là bắt buộc',
            'pay_period.in' => 'Kỳ hạn thanh toán không hợp lệ',
            'price.required' => 'Giá thuê là bắt buộc',
            'price.min' => 'Giá thuê phải lớn hơn hoặc bằng 0',
            'electricity_pay_type.required' => 'Cách trả tiền điện là bắt buộc',
            'electricity_pay_type.in' => 'Cách trả tiền điện không hợp lệ',
            'electricity_price.required' => 'Giá điện là bắt buộc',
            'electricity_price.min' => 'Giá điện phải lớn hơn hoặc bằng 0',
            'electricity_number_start.required' => 'Số điện ban đầu là bắt buộc',
            'electricity_number_start.min' => 'Số điện ban đầu phải lớn hơn hoặc bằng 0',
            'water_pay_type.required' => 'Cách trả tiền nước là bắt buộc',
            'water_pay_type.in' => 'Cách trả tiền nước không hợp lệ',
            'water_price.required' => 'Giá nước là bắt buộc',
            'water_price.min' => 'Giá nước phải lớn hơn hoặc bằng 0',
            'water_number_start.required' => 'Số nước ban đầu là bắt buộc',
            'water_number_start.min' => 'Số nước ban đầu phải lớn hơn hoặc bằng 0',
            'number_of_tenant_current.required' => 'Số người ở là bắt buộc',
            'number_of_tenant_current.min' => 'Số người ở phải lớn hơn hoặc bằng 1',
            'number_of_tenant_current.max' => 'Số người ở vượt quá giới hạn của phòng',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ',
            'end_date.date' => 'Ngày kết thúc không hợp lệ',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
        ];
    }
}