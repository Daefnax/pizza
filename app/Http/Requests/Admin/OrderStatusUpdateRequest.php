<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(Order::ALLOWED),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Поле "статус" обязательно для заполнения.',
            'status.in'       => 'Недопустимое значение статуса.',
        ];
    }
}
