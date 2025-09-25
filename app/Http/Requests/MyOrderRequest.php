<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MyOrderRequest extends FormRequest
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
            //
            'doctor_id' => ['required', 'exists:doctors,id'],
            'started_at' => ['required', 'date',
                        function ($attribute, $value, $fail) {
                            $date = Carbon::parse($value)->startOfDay();
                            $min = now()->addDays(1)->startOfDay();
                            $max = now()->addDays(3)->endOfDay();

                            if($date->lt($min) || $date->gt($max))
                            {
                                $fail('The '.$attribute.' must be between '. $min->toDateString() .' and '. $max->toDateString() .'.');
                            }
                        }],
            'time_at' => ['required', 'date_format:H:i',
                        Rule::in(['09:00', '10:00', '11:00', '13:00', '14:00', '15:00'])],
            'proof' => ['required', 'image', 'max:2048'], // max 2MB
        ];
    }
}
