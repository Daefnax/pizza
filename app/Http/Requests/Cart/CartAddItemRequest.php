<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartAddItemRequest extends FormRequest
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
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'quantity'   => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'product_id обязателен.',
            'product_id.integer' => 'product_id должен быть целым числом.',
            'product_id.exists' => 'Товар не найден.',
            'quantity.required' => 'quantity обязателен.',
            'quantity.integer' => 'quantity должен быть целым числом.',
            'quantity.min' => 'Минимум 1.',
            'quantity.max' => 'Максимум 20 на позицию.',
        ];
    }
}
