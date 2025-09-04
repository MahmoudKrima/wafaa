<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'term_description_ar' => ['required', 'string', 'max:999'],
            'term_description_en' => ['required', 'string', 'max:999'],
            'policy_description_ar' => ['required', 'string', 'max:999'],
            'policy_description_en' => ['required', 'string', 'max:999'],

        ];
    }
}
