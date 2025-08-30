<?php

namespace App\Http\Requests\Admin\Shipping;

use App\Http\Requests\User\Shipping\StoreShippingRequest;

class StoreAdminShippingRequest extends StoreShippingRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge([
            'user_id' => 'required|exists:users,id'
        ], $rules);

        return $rules;
    }

    public function messages()
    {
        $messages = parent::messages();

        $messages['user_id.required'] = __('admin.user_id_required');
        $messages['user_id.exists'] = __('admin.user_not_found');

        return $messages;
    }
}
