<?php

namespace App\Http\Requests\User\Transaction;

use App\Enum\TransactionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'code' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'bank' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('banks', 'id')
                    ->where('admin_id', auth()->user()->created_by)
                    ->where('status', 'active')
            ],
            'status' => [
                'sometimes',
                'nullable',
                'string',
                'max:10',
                Rule::in(TransactionStatusEnum::vals())
            ]
        ];
    }
}
