<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ManufactureContactRequest extends FormRequest
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
        $contact = $this->route('contact');

        return [
            'name' => ['required', 'string'],
            'phone' => [
                'nullable',
                'string',
                Rule::unique('manufacture_contacts')
                    ->ignore($contact?->id),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique('manufacture_contacts')
                    ->ignore($contact?->id),
            ],
            'position' => ['nullable', 'string'],
            'manufacture_id' => ['required', 'exists:manufactures,id'],
        ];
    }



    protected function failedValidation(Validator $validator)
    {
        $errorBag = $this->route('contact') ? 'editContact' : 'createContact';

        throw new \Illuminate\Validation\ValidationException(
            $validator,
            redirect()
                ->back()
                ->withInput()
                ->withErrors($validator, $errorBag)
        );
    }
}
