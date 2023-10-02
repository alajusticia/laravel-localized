<?php

namespace ALajusticia\Localized\Http\Requests;

use ALajusticia\Localized\Localized;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocaleRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'locale' => [
                'nullable',
                'string',
                Rule::in(Localized::availableLocales()),
            ],
        ];
    }
}
