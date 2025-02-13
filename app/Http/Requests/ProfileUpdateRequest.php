<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'country_id' => ['required', 'exists:countries,id'],
            'dial_code' => ['required'],
            'phone' => ['required'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('public.name'),
            'username' => trans('public.username'),
            'email' => trans('public.email'),
            'country_id' => trans('public.country'),
            'dial_code' => trans('public.dial_code'),
            'phone' => trans('public.phone_number'),
        ];
    }
}
