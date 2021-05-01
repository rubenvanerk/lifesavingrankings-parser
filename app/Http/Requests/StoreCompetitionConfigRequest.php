<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'city' => 'required',
            'country_id' => 'required',
        ];
    }
}
