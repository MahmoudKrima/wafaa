<?php

namespace App\Http\Requests\Admin\Service;

use Illuminate\Validation\Rule;
use App\Enum\ActivationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            "title" => [
                "required",
                'max:255',
                'string',
            ],            
            "description" => [
                "required",
                'max:65000',
                'string',
            ],
            'image' => [
                'required',
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
