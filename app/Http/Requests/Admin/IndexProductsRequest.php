<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('admin');
    }

    public function rules(): array
    {
        return [
            'type'     => ['nullable', Rule::in(Product::TYPES)],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
