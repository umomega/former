<?php

namespace Umomega\Former\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForm extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable',
        ];
    }
}