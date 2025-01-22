<?php

namespace App\Http\Requests\User;

use App\Models\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_name' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9]+$/'
            ],
            'email' => [
                'required_without:phone',
                'nullable',
                'email',
                'max:255',
                'unique:users'
            ],
            'phone' => [
                'required_without:email',
                'nullable',
                'string',
                'max:20',
                'unique:users',
                'regex:/^([0-9\s\-\+\(\)]*)$/'
            ],
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
            ],

            // Profile fields
            'display_name' => ['required', 'string', 'min:5', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255', 'alpha'],
            'last_name' => ['nullable', 'string', 'max:255', 'alpha'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'user_name.regex' => 'Username can only contain letters, numbers, dashes, and underscores.',
            'phone.regex' => 'Phone number format is invalid.',
            'required_without' => 'Either email or phone is required.',
        ];
    }
}
