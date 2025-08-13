<?php

namespace App\Http\Requests\Admin\FrontSetting\About;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title" => ["required", 'max:255', 'string'],
            "description" => ["required", 'string'],
        ];
    }
}
