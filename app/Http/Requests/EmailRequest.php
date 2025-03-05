<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailRequest extends FormRequest
{
//    public mixed $manufacture_id;
//    public mixed $email;

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
        $email = $this->route('email');

        return [
            'email' => [
                'required',
                'email',
                Rule::unique('emails')->ignore($email ? $email->id : null),
            ],
            'manufacture_id' => ['required', 'exists:manufactures,id'],
        ];
    }
}
