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
            'email' => [
                'required',
                'email:dns,filter',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!$this->validateEmailDeliverability($value)) {
                        $fail(__('admin.wrong_mail'));
                    }
                }
            ],
            'phone' => [
                'required',
                'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
            ],
            'whatsapp' => [
                'required',
                'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
            ],
        ];
    }

    protected function validateEmailDeliverability($email)
    {
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }
        return true;
    }
}
