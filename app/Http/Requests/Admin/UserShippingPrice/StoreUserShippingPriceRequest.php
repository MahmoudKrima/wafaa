<?php

namespace App\Http\Requests\Admin\UserShippingPrice;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserShippingPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'string', 'max:255', Rule::unique('user_shipping_prices', 'company_id')->where('user_id', $this->route('user')->id)],
            'company_name_ar' => ['required', 'string', 'max:255'],
            'company_name_en' => ['required', 'string', 'max:255'],
            'international_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'local_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
