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
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:about_items,id'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.description' => ['required', 'string'],
        ];
    }
}
