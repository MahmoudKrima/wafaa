<?php

namespace App\Http\Requests\Admin\Contact;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReplyContactRequest extends FormRequest
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
            'contact' => [
                'required',
                'integer',
                Rule::exists('contacts', 'id')
                    ->where('status', 'pending')
            ],
            'message' => [
                'required',
                'string',
            ]
        ];
    }
}
