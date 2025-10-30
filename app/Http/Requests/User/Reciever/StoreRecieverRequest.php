<?php

namespace App\Http\Requests\User\Reciever;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecieverRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'nullable',
                'email:dns,filter',
                
                function ($attribute, $value, $fail) {
                    if (!$this->validateEmailDeliverability($value)) {
                        $fail(__('admin.wrong_mail'));
                    }
                }
            ],
            'phone' => [
                'required',
                'string',
                //'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
                
              
            ],
            'additional_phone' => [
                'nullable',
                'sometimes',
                'string',
                //'regex:/^(05|5|9665|96605|009665|\+9665)[0-9]{8}$/',
                
               
            ],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:99999'],
           
        ];
    }

    protected function validateEmailDeliverability($email)
    {
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }
        return true;
    }
}
