<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApartmentRoomRequest extends FormRequest
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
            'apartment_id' => 'required|exists:apartments,id',
            'room_number' => 'required|string|max:45',
            'default_price' => 'required|numeric|min:0',
            'max_tenant' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
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
            'room_number.required' => 'Số phòng là bắt buộc',
            'default_price.required' => 'Giá thuê là bắt buộc',
        ];
    }
}