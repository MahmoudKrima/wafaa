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
            'term_description_ar' => [
                'required',
                'string'
            ],
            'term_description_en' => [
                'required',
                'string'
            ],
            'policy_description_ar' => [
                'required',
                'string'
            ],
            'policy_description_en' => [
                'required',
                'string'
            ],
        ];
    }
}
