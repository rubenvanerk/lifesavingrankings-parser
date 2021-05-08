<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:rankings.rankings_competition'],
            'date' => 'required',
            'city' => 'required',
            'country_id' => 'required',
            'comment' => 'nullable',
            'original_name' => 'nullable',
            'type_of_timekeeping' => ['required', 'integer', 'in:0,1,2'],
        ];
    }
}
