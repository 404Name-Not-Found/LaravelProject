<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreDonationRequest
 *
 * Validates the donation submission payload, ensuring that the contribution amount
 * is a positive numeric value and the donation date is a valid date.
 */
class StoreDonationRequest extends FormRequest
{
    // @ai-start: form request authorizing and validating donation submissions
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true because application authentication is out of scope.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'Donation_Amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'Donation_Date' => ['required', 'date'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Donation_Amount.required' => 'Please specify a donation amount.',
            'Donation_Amount.numeric' => 'The donation amount must be a valid number.',
            'Donation_Amount.min' => 'The donation amount must be at least $0.01.',
            'Donation_Date.required' => 'Please provide the donation date.',
            'Donation_Date.date' => 'The donation date must be a valid date format.',
        ];
    }
    // @ai-end
}
