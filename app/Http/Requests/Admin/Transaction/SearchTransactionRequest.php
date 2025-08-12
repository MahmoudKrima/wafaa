<?php

namespace App\Http\Requests\Admin\Transaction;

use Illuminate\Validation\Rule;
use App\Enum\TransactionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class SearchTransactionRequest extends FormRequest
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
            'code' => ['sometimes', 'nullable', 'string'],
            'user_id' => ['sometimes', 'nullable', 'string', Rule::exists('users', 'id')->where('created_by', getAdminIdOrCreatedBy())],
            'bank' => ['sometimes', 'nullable', 'string', Rule::exists('banks', 'id')],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(TransactionStatusEnum::vals())],
        ];
    }
}
