<?php

namespace App\Http\Requests\Admin\Slider;

use App\Enum\ActivationStatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSliderRequest extends FormRequest
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
            "title" => ["required", 'max:255', 'string'],
            "description" => ["required", 'string'],
            "subtitle" => ['nullable', 'string', 'max:255'],
            "button_text" => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'url'],
            'image' => ['required', 'image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'mimes:jpg,jpeg,jfif,png,gif,webp'],
            'status' => ['required', 'string', Rule::in(ActivationStatusEnum::vals())],
        ];
    }
}
