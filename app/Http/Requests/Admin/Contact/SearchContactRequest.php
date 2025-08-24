<?php

namespace App\Http\Requests\Admin\Contact;

use App\Enum\ContactStatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SearchContactRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'status' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(ContactStatusEnum::vals())
            ],
        ];
    }
}
