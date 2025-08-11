<?php

namespace App\Http\Requests\User\Transaction;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:999999'
            ],
            'banks_id' => [
                'required',
                'integer',
                Rule::exists('banks', 'id')
                    ->where('admin_id', auth()->user()->created_by)
                    ->where('status', 'active')
            ],
            'attachment' => [
                'required',
                'image',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif',
                'mimes:jpg,jpeg,jfif,png,gif,webp',
                'max:5120'
            ]
        ];
    }
}
