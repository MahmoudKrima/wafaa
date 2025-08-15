<?php

namespace App\Http\Requests\Admin\FrontSetting\About;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title" => ["required", 'max:255', 'string'],
            "subtitle" => ["required", 'max:255', 'string'],
            'image' => ['nullable', 'image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'mimes:jpg,jpeg,jfif,png,gif,webp'],
        ];
    }
}
