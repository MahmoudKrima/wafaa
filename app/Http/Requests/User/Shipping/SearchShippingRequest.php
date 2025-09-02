<?php

namespace App\Http\Requests\User\Shipping;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class SearchShippingRequest extends FormRequest
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
            'dateFrom'          => ['nullable', 'date'],
            'dateTo'            => ['nullable', 'date', 'after_or_equal:dateFrom'],
            'isCod'             => ['nullable', 'in:true,false'],
            'shippingCompanyId' => ['nullable', 'string'],
            'method'            => ['nullable', 'in:local,international'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $from = $this->normalizeDate($this->input('dateFrom'));
        $to   = $this->normalizeDate($this->input('dateTo'));

        $this->merge([
            'dateFrom' => $from,
            'dateTo'   => $to,
        ]);
    }

    private function normalizeDate(?string $value): ?string
    {
        if (!$value) return null;

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
