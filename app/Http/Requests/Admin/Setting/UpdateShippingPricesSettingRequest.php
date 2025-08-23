<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingPricesSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'extra_weight_price' => ['required', 'numeric', 'min:0'],
            'cash_on_delivery_price' => ['required', 'numeric', 'min:0'],
        ];
    }

  
}
