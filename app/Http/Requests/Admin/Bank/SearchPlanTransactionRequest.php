<?php

namespace App\Http\Requests\Admin\Bank;

use Illuminate\Validation\Rule;
use App\Enum\PlanTransactionEnum;
use App\Models\Plan;
use App\Models\Provider;
use Illuminate\Foundation\Http\FormRequest;

class SearchPlanTransactionRequest extends FormRequest
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
            'provider' => ['sometimes', 'nullable', 'string', function ($attribute, $value, $fail) {
                if (isset($value)) {
                    $provider = Provider::whereHas('planTransactions', fn ($query) => $query->where('bank_id', $this->route('bank')->id))
                        ->first();
                    if (!$provider) {
                        $fail(__('admin.not_found_data'));
                    }
                }
            }],
            'plan' => ['sometimes', 'nullable', 'string', function ($attribute, $value, $fail) {
                if (isset($value)) {
                    $plan = Plan::whereHas('planTransactions', fn ($query) => $query->where('bank_id', $this->route('bank')->id))
                        ->first();
                    if (!$plan) {
                        $fail(__('admin.not_found_data'));
                    }
                }
            }],
            'name' => ['sometimes', 'nullable', 'string'],
            'date_from' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d', function ($attribute, $value, $fail) {
                if (isset($value) && request()->input('date_to') != null && $value >= request()->input('date_to')) {
                    $fail(__('admin.date_from_must_be_smaller_than_date_to'));
                }
            }],
            'date_to' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            'paid_amount_from' => ['sometimes', 'nullable', 'numeric', 'min:0', function ($attribute, $value, $fail) {
                if (isset($value) && request()->input('paid_amount_to') != null && $value >= request()->input('paid_amount_to')) {
                    $fail(__('admin.paid_amount_from_must_be_less_than_paid_amount_to'));
                }
            }],
            'paid_amount_to' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'transaction_number' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(PlanTransactionEnum::vals())]
        ];
    }
}
