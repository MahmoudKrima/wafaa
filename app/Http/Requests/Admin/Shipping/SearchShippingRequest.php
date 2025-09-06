<?php

namespace App\Http\Requests\Admin\Shipping;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchShippingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'dateFrom'          => ['nullable', 'date'],
            'dateTo'            => ['nullable', 'date', 'after_or_equal:dateFrom'],
            'isCod'             => ['nullable', 'in:true,false'],
            'shippingCompanyId' => ['nullable', 'string'],
            'method'            => ['nullable', 'in:local,international'],
            'type'              => ['nullable', 'in:box,document'],
            'status'            => ['nullable', 'in:pending,processing,failed,cancelRequest,canceled,returned,delivered'],
            'search'            => ['nullable', 'string'],
            'receiverName'        => ['nullable', 'string'],
            'receiverPhone'       => ['nullable', 'string'],
            'userId'              => ['nullable', 'array'],
            'userId.*'            => ['nullable', 'string', Rule::exists('users', 'id')->where('created_by', getAdminIdOrCreatedBy())],
        ];
    }

    protected function prepareForValidation(): void
    {
        $from = $this->normalizeDate($this->input('dateFrom'));
        $to   = $this->normalizeDate($this->input('dateTo'));
        $rawPhone = $this->input('receiverPhone');
        $senderPhone = $this->normalizeSaudiPhone($rawPhone);
        $this->merge([
            'dateFrom'    => $from,
            'dateTo'      => $to,
            'receiverPhone' => $senderPhone,
            'receiverName'  => trim((string) $this->input('receiverName')),
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

    private function normalizeSaudiPhone(?string $value): ?string
    {
        if (!$value) return null;
        $digits = preg_replace('/\D+/', '', $value ?? '');

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '966')) {
            $digits = substr($digits, 3);
        }
        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
        }

        return '+966-' . $digits;
    }
}
