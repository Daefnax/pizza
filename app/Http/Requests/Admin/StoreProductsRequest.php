<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Product::TYPES)],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название товара обязательно.',
            'name.string' => 'Название должно быть строкой.',
            'name.max' => 'Название не должно превышать :max символов.',

            'type.required' => 'Тип товара обязателен.',
            'type.in' => 'Тип товара должен быть либо "pizza", либо "drink".',

            'price.required' => 'Цена обязательна.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть меньше :min.',

            'is_active.required' => 'Статус активности обязателен.',
            'is_active.boolean' => 'Статус активности должен быть true или false.',
        ];
    }
}
