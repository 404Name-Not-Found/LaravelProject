<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Class StoreEventRegistrationRequest
 *
 * Validates the event registration request payload, ensuring an event is selected,
 * exists in the database, and is not a duplicate registration for the contact.
 */
class StoreEventRegistrationRequest extends FormRequest
{
    // @ai-start: form request authorizing and validating event registration submissions
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
            'Event_ID' => ['required', 'integer', 'exists:events,Event_ID'],
        ];
    }

    /**
     * Configure the validator instance with business logic checks.
     *
     * @param  Validator  $validatorInstance  The validator instance to attach after-validation hooks to.
     */
    public function withValidator(Validator $validatorInstance): void
    {
        $validatorInstance->after(function (Validator $currentValidator) {
            $targetContact = $this->route('contact');
            $selectedEventId = (int) $this->input('Event_ID');

            if ($targetContact && $selectedEventId > 0) {
                $isAlreadyRegisteredForEvent = $targetContact->events()
                    ->where('events.Event_ID', $selectedEventId)
                    ->exists();

                if ($isAlreadyRegisteredForEvent) {
                    $currentValidator->errors()->add(
                        'Event_ID',
                        'This member is already registered for the selected event.'
                    );
                }
            }
        });
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Event_ID.required' => 'Please select an event to register for.',
            'Event_ID.integer' => 'The selected event identifier must be a valid number.',
            'Event_ID.exists' => 'The selected event does not exist in the database.',
        ];
    }
    // @ai-end
}
