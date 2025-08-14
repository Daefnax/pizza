<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_email'   => ['required', 'string', 'email', 'max:255'],
            'customer_phone'   => ['required', 'string', 'max:255'],
            'customer_address' => ['required', 'string', 'max:500'],
            'delivery_time'    => ['required', 'date', 'after:now'],
        ];
    }
}
