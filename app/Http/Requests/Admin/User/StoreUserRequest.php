<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'address' => ['required', 'string', 'max:999'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
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
                Rule::unique('users', 'phone'),
                Rule::unique('users', 'additional_phone'),
                Rule::unique('admins', 'phone'),
            ],
            'additional_phone' => [
                'nullable',
                'string',
                'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
                Rule::unique('users', 'additional_phone'),
                Rule::unique('admins', 'phone'),
                Rule::unique('users', 'phone'),
            ],
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')],
            'password' => ['required', 'string', 'min:8', Password::min(8)
                ->max(50)
                ->letters()
                ->mixedCase()
                ->symbols()],
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
