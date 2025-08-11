<?php

namespace App\Http\Requests\User\Profile;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $types = User::$types;
        return [
            'name' => ['required', 'string'],
            'bio' => ['required', 'string'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:Users,email,' . auth('web')->id()],
            'phone' => ['required', 'digits_between:8,15', 'unique:Users,phone,' . auth('web')->id()],
            'whatsapp' => ['required', 'digits_between:8,15', 'unique:Users,whatsapp,' . auth('web')->id()],
            'password' => ['nullable', 'min:8', 'max:30'],
            'country_id' => ['required', 'exists:countries,id'],
            'image' => ['image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'mimes:jpg,jpeg,jfif,png,gif,webp'],
            'sales_hours' => ['required', 'string'],
            'type' => ['required', Rule::in($types), function ($attribute, $value, $fail) {
                $currentUserType = auth('web')->user()->type->value;
                $inputType = request()->input('type');

                if ($currentUserType == 'private' && $inputType == 'agency') {
                    // User is changing from private to agency, so cover is required
                    if (!request()->hasFile('cover')) {
                        $fail(__('validation.required', ['attribute' => __('admin.cover')]));
                    }
                } elseif ($currentUserType == 'agency' && $inputType == 'private') {
                    // User is changing from agency to private
                    if (request()->hasFile('cover')) {
                        // User provided a cover, which is not allowed
                        $fail(__('validation.cover_not_allowed'));
                    }
                } elseif ($currentUserType == 'agency' && $inputType == 'agency' && auth('web')->user()->cover == null) {
                    if (!request()->hasFile('cover')) {
                        $fail(__('validation.required', ['attribute' => __('admin.cover')]));
                    }
                }
            }],
            'cover' => [
                'nullable',
                'image',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif',
                'mimes:jpg,jpeg,jfif,png,gif,webp'
            ],
        ];
    }
}
