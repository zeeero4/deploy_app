<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobOfferRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $route = $this->route()->getName();

        $rule = [
            'title' => 'required|string|max:50',
            'occupation_id' => 'required|exists:occupations,id',
            'due_date' => 'required|after_or_equal:today',
            'description' => 'required|string|max:2000',
            'is_published' => 'nullable|boolean',
        ];

        if ($route === 'job_offer.update') {
            $rule['due_date'] = 'required|date';
        }

        return $rule;
    }
}