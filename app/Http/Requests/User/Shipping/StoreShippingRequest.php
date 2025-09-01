<?php

namespace App\Http\Requests\User\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'shipping_company_id'        => ['required', 'string', 'max:64'],
            'shipping_method'            => ['required', 'in:local,international'],
            'sender_name'                => ['required', 'string', 'max:255'],
            'sender_phone'               => ['required', 'regex:/^05\d{8}$/'],
            'sender_email'               => ['nullable', 'email', 'max:255'],
            'sender_address'             => ['required', 'string', 'max:500'],
            'sender_country_id'  => ['required', 'string'],
            'sender_country_name' => ['required', 'string'],
            'sender_state_id'    => ['required', 'string'],
            'sender_state_name'  => ['required', 'string'],
            'sender_city_id'     => ['required', 'string'],
            'sender_city_name'   => ['required', 'string'],
            'sender_postal_code'         => ['nullable', 'string', 'max:20'],
            'selected_receivers'         => ['required', 'string', 'json'],
            'receivers_count'            => ['required', 'integer', 'min:1'],
            'package_type'               => ['required', 'in:box,document'],
            'package_number'             => ['required', 'integer', 'min:1'],
            'length'                     => ['required', 'numeric', 'min:0.1'],
            'width'                      => ['required', 'numeric', 'min:0.1'],
            'height'                     => ['required', 'numeric', 'min:0.1'],
            'weight'                     => ['required', 'numeric', 'min:0.1'],
            'cod_amount'                     => ['sometimes', 'nullable','numeric', 'min:0'],
            'package_description'        => ['nullable', 'string', 'max:1000'],
            'payment_method'             => ['required', 'in:wallet,cod'],
            'shipping_price_per_receiver' => ['required', 'numeric', 'min:0'],
            'extra_weight_per_receiver'  => ['required', 'numeric', 'min:0'],
            'cod_price_per_receiver'     => ['required_if:payment_method,cod', 'numeric', 'min:0'],
            'total_per_receiver'         => ['required', 'numeric', 'min:0'],
            'total_amount'               => ['required', 'numeric', 'min:0'],
            'currency'                   => ['required', 'string', 'max:6'],
            'max_weight'                 => ['required', 'numeric', 'min:0'],
            'entered_weight'             => ['required', 'numeric', 'min:0.1'],
            'extra_kg'                   => ['required', 'numeric', 'min:0'],
            'accept_terms'               => ['accepted'],
            'shipment_image'             => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function passedValidation(): void
    {
        $raw = $this->input('selected_receivers');
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $this->failedValidationOnce('selected_receivers', __('validation.custom.selected_receivers.invalid_json'));
        } elseif (count($decoded) === 0) {
            $this->failedValidationOnce('selected_receivers', __('validation.custom.selected_receivers.empty'));
        } else {
            foreach ($decoded as $i => $r) {
                if (!(isset($r['id']) || isset($r['name']))) {
                    $this->failedValidationOnce('selected_receivers', __('validation.custom.selected_receivers.missing_fields'));
                    break;
                }
            }
        }
        $entered = (float)$this->input('entered_weight');
        $max     = (float)$this->input('max_weight');
        $extra   = (float)$this->input('extra_kg');

        $expected = max(0, round($entered - $max, 2));
        if (abs($expected - $extra) > 0.01) {
            $this->failedValidationOnce('extra_kg', __('validation.custom.extra_kg.mismatch'));
        }
    }

    private function failedValidationOnce(string $field, string $message): void
    {
        $validator = validator($this->all(), []);
        $validator->after(function ($v) use ($field, $message) {
            $v->errors()->add($field, $message);
        });
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
