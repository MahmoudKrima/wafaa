<?php

namespace App\Http\Requests\Admin\WalletLogs;

use App\Enum\WalletLogTypeEnum;
use Illuminate\Validation\Rule;
use App\Enum\TransactionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class SearchWalletLogsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
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

            'trans_type' => [
                'sometimes',
                'nullable',
                'string',
                'max:10',
                Rule::in(WalletLogTypeEnum::vals())
            ],
            'type' => [
                'sometimes',
                'nullable',
                'string',
                'max:10',
                Rule::in(TransactionTypeEnum::vals())
            ]
        ];
    }
}
