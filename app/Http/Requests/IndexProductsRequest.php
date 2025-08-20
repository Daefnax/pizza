<?php

namespace App\Http\Requests;

use App\DTO\IndexProductsDTO;
use App\Enums\ProductType;
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

    public function toDTO(): IndexProductsDTO
    {
        return new IndexProductsDTO(
            type: $this->validated('type'),
            perPage: (int)($this->validated('per_page') ?? 20),
        );
    }
}
