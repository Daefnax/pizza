<?php

namespace App\Http\Requests;

use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'      => ['nullable', Rule::enum(ProductType::class)],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['integer|min:1|max:100']
        ];
    }
}
