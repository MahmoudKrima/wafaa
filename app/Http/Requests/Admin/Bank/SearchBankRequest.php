<?php

namespace App\Http\Requests\Admin\Bank;

use App\Enum\ActivationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchBankRequest extends FormRequest
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
            'name' => ['sometimes', 'nullable', 'string'],
            'account_owner' => ['sometimes', 'nullable', 'string'],
            'account_number' => ['sometimes', 'nullable', 'string'],
            'iban_number' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(ActivationStatusEnum::vals())],
        ];
    }
}
