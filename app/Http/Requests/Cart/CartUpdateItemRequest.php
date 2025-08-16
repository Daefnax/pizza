<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CartUpdateItemRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:20']
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'quantity обязателен.',
            'quantity.integer' => 'quantity должен быть целым числом.',
            'quantity.min' => 'Минимум 1.',
            'quantity.max' => 'Максимум 20 на позицию.',
        ];
    }
}
