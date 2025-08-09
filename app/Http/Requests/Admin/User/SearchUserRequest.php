<?php

namespace App\Http\Requests\Admin\User;

use App\Models\City;
use Illuminate\Validation\Rule;
use App\Enum\ActivationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class SearchUserRequest extends FormRequest
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
        $rules = [];
        $rules['name'] = ['sometimes', 'nullable', 'string', 'max:255'];
        $rules['email'] = ['sometimes', 'nullable', 'string', 'max:255'];
        $rules['phone'] = ['sometimes', 'nullable', 'digits_between:1,15'];
        $rules['status'] = ['sometimes', 'nullable', 'string', Rule::in(ActivationStatusEnum::vals())];
        return $rules;
    }
}
