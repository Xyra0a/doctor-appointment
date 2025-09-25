<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HospitalRequest extends FormRequest
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
        $id = $this->route('hospital') ? $this->route('hospital')->id : null;
        return [
            //
            'name' => ['required', 'string', 'max:255', 'unique:hospitals,name,'.$id],
            'photo' => $this->isMethod('post') ? ['required', 'image', 'max:2048'] : ['sometimes', 'image', 'max:2048'],
            'about' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'post_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:15'],
        ];
    }
}
