<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Validation\Rule;
use App\Enum\ActivationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class SearchAdminRequest extends FormRequest
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
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/'],
            'role' => ['sometimes', 'nullable', 'string', 'exists:roles,id'],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(ActivationStatusEnum::vals())],
        ];
    }
}
