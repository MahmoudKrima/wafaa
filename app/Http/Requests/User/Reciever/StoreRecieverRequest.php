<?php

namespace App\Http\Requests\User\Reciever;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecieverRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:dns,filter',
                Rule::unique('recievers', 'email'),
                Rule::unique('users', 'email'),
                Rule::unique('admins', column: 'email'),
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
                Rule::unique('recievers', 'phone'),
                Rule::unique('recievers', 'additional_phone'),
                Rule::unique('users', 'phone'),
                Rule::unique('users', 'additional_phone'),
                Rule::unique('admins', 'phone'),
            ],
            'additional_phone' => [
                'nullable',
                'string',
                'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
                Rule::unique('recievers', 'additional_phone'),
                Rule::unique('recievers', 'phone'),
                Rule::unique('admins', 'phone'),
                Rule::unique('users', 'phone'),
                Rule::unique('users', 'additional_phone'),
            ],
            'postal_code' => ['required', 'string', 'max:255'],
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
