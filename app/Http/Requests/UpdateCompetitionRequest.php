<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompetitionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('rankings.rankings_competition')->ignore($this->competition),
            ],
            'date' => 'required',
            'city' => 'required',
            'country_id' => 'required',
            'comment' => 'nullable',
            'original_name' => 'nullable',
            'type_of_timekeeping' => ['required', 'integer', 'in:0,1,2'],
        ];
    }
}
