<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManufactureRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'web' => ['required', 'url'],
            'adress_loading' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'nottypicalproduct'=> ['nullable', 'boolean'],
            'checkmanufacture'=> ['nullable', 'boolean'],
            'date_contract'=> ['nullable', 'boolean'],
            'region'=> ['nullable', 'exists:federal_dists,id'],
            'city'=> ['nullable', 'exists:federal_dists,id'],
        ];
    }
}
