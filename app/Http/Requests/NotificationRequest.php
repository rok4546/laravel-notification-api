<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For testing/demo purposes, allow all requests
        // In production, implement proper authorization
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
            'channel' => [
                'required',
                Rule::in(['email', 'sms', 'slack']),
            ],
            'message' => [
                'required',
                'string',
                'max:255',
            ],
            'notifiable_type' => [
                'required',
                Rule::in(['App\Models\User', 'App\Models\Post']),
            ],
            'notifiable_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $notifiableType = $this->input('notifiable_type');
                    
                    if (!$notifiableType) {
                        return;
                    }

                    // Check if the notifiable_id exists in the specified table
                    $model = new $notifiableType();
                    $exists = $model->find($value);

                    if (!$exists) {
                        $fail("The specified {$notifiableType} does not exist.");
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'channel.required' => 'Channel is required.',
            'channel.in' => 'Channel must be one of: email, sms, slack.',
            'message.required' => 'Message is required.',
            'message.max' => 'Message cannot exceed 255 characters.',
            'notifiable_type.required' => 'Notifiable type is required.',
            'notifiable_type.in' => 'Notifiable type must be App\Models\User or App\Models\Post.',
            'notifiable_id.required' => 'Notifiable ID is required.',
            'notifiable_id.integer' => 'Notifiable ID must be an integer.',
        ];
    }
}
