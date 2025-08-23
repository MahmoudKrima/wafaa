<?php

namespace App\Http\Requests\Admin\Partner;

use Illuminate\Validation\Rule;
use App\Enum\ActivationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
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
            'image' => [
                'image',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif',
                'mimes:jpg,jpeg,jfif,png,gif,webp',
                'max:5120'
            ],
            'status' => [
                'required',
                'string',
                Rule::in(ActivationStatusEnum::vals())
            ],
        ];
    }
}
