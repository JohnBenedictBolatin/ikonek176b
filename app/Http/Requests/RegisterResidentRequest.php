<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterResidentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // change if you have custom auth logic
    }


    public function rules()
    {
        return [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'birthdate' => 'nullable|date',
            'sex' => 'nullable|in:Male,Female',
            'civil_status' => 'nullable|string|max:50',
            'contact_number' => ['required','digits:10'], // frontend uses 10 digits (without +63)
            'secondary_number' => ['nullable','digits:10'],
            'password' => 'required|string|min:6',
            'home_address' => 'required|string|max:1000',
            'phase' => 'nullable|string',
            'package' => 'nullable|string',
            'proof_of_residency' => 'nullable|string',
            'fk_role_id' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'contact_number.digits' => 'Contact number must be 10 digits (without country code).',
            'secondary_number.digits' => 'Secondary number must be 10 digits (without country code).',
        ];
    }

    // public function prepareForValidation()
    // {
    //     // normalize phone: remove non-digit characters (optionally)
    //     if ($this->has('contact_number')) {
    //         $this->merge(['contact_number' => preg_replace('/\D+/', '', $this->contact_number)]);
    //     }


    //     if ($this->has('secondary_number')) {
    //         $this->merge(['secondary_number' => preg_replace('/\D+/', '', $this->secondary_number)]);
    //     }
    // }
}