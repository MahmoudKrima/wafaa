<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token' => ['required', 'string', Rule::exists('Users', 'token')->where('email', request()->input('email'))->where('status', 'active')],
            'email' => ['required', 'email', Rule::exists('Users', 'email')->where('token', request()->input('token'))->where('status', 'active')],
            'new_password' => ['required', 'min:8', 'max:30', 'confirmed'],
        ];
    }
}
