<?php

namespace App\Http\Requests\Admin\Bank;

use App\Enum\ActivationStatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBankRequest extends FormRequest
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
            "name_ar" => ["required", 'max:255', 'string', Rule::unique('banks', 'name->ar')],
            "name_en" => ["required", 'max:255', 'string', Rule::unique('banks', 'name->en')],
            'account_owner_ar' => ['required', 'string', 'max:255'],
            'account_owner_en' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'iban_number' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'mimes:jpg,jpeg,jfif,png,gif,webp'],
            'status' => ['required', 'string', Rule::in(ActivationStatusEnum::vals())],
        ];
    }
}
