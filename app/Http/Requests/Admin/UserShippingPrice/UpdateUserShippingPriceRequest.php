<?php

namespace App\Http\Requests\Admin\UserShippingPrice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserShippingPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'string', 'max:255', Rule::unique('user_shipping_prices', 'company_id')->where('user_id', $this->route('user')->id)->ignore($this->route('userShippingPrice'))],
            'company_name_ar' => ['required', 'string', 'max:255'],
            'company_name_en' => ['required', 'string', 'max:255'],
            'international_price' => ['required', 'numeric', 'min:0'],
            'local_price' => ['required', 'numeric', 'min:0'],
        ];
    }

}
