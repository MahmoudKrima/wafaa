<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'email:dns,filter',
                'max:255',
                Rule::unique('admins', 'email'),
                function ($attribute, $value, $fail) {
                    if (!$this->validateEmailDeliverability($value)) {
                        $fail(__('admin.wrong_mail'));
                    }
                }
            ],
            'phone' => [
                'required',
                'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
                Rule::unique('admins', 'phone'),
                Rule::unique('users', 'phone'),
                Rule::unique('users', 'additional_phone'),
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->max(50)
                    ->letters()
                    ->mixedCase()
                    ->symbols()
            ],
            'image' => [
                'required',
                'image',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif',
                'mimes:jpg,jpeg,jfif,png,gif,webp',
                'max:5120'
            ],
            'role' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
            ]
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
