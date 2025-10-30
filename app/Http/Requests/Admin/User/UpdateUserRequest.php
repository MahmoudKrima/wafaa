<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                //'email:dns,filter',
                Rule::unique('users', 'email')->ignore($this->route('user')->id),
                Rule::unique('admins', 'email'),
                function ($attribute, $value, $fail) {
                    if (!$this->validateEmailDeliverability($value)) {
                        $fail(__('admin.wrong_mail'));
                    }
                }
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
                Rule::unique('users', 'phone')->ignore($this->route('user')->id),
                //Rule::unique('users', 'additional_phone'),
                Rule::unique('admins', 'phone'),
            ],
            'additional_phone' => [
                'nullable',
                'string',
                'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
                //Rule::unique('users', 'additional_phone')->ignore($this->route('user')->id),
                Rule::unique('admins', 'phone'),
                //Rule::unique('users', 'phone'),
            ],
            'password' => ['nullable', 'string', 'min:8', Password::min(8)
                ->max(50)
                ->letters()
                ->mixedCase()
                ->symbols()],
            'balance' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999999.99'],
            'shipping_prices'                   => ['required', 'array'],
            'shipping_prices.*'                 => ['required', 'array'],
            'shipping_prices.*.id'              => ['required', 'string'],
            'shipping_prices.*.name'            => ['required', 'string', 'max:255'],

            'shipping_prices.*.require_local'         => ['nullable', 'in:0,1'],
            'shipping_prices.*.require_international' => ['nullable', 'in:0,1'],

            'shipping_prices.*.localprice' => [
                'required_if:shipping_prices.*.require_local,1',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'shipping_prices.*.internationalprice' => [
                'required_if:shipping_prices.*.require_international,1',
                'numeric',
                'min:0',
                'max:999999.99',
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
