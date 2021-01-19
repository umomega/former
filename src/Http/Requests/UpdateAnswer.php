<?php

namespace Umomega\Former\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnswer extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|integer',
            'notes' => 'nullable',
        ];
    }
}