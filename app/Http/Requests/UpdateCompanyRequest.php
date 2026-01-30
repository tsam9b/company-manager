<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = Company::rules();

        if (is_string($this->input('logo'))) {
            $rules['logo'] = ['nullable', 'string'];
        }

        return $rules;
    }
}
