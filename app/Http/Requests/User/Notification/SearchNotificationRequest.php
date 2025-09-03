<?php

namespace App\Http\Requests\User\Notification;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Enum\NotificationTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class SearchNotificationRequest extends FormRequest
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
            'type'      => ['nullable', Rule::in(NotificationTypeEnum::vals())],
        ];
    }
}
