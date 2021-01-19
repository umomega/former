<?php

namespace Umomega\Former\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormField extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'label' => 'required|max:255',
            'description' => 'nullable',
            'is_visible' => 'required|boolean',
            'rules' => 'nullable',
            'default_value' => 'nullable',
            'options' => 'nullable|json'
        ];
    }
}