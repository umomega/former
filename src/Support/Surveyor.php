<?php

namespace Umomega\Former\Support;

use Illuminate\Http\Request;
use Umomega\Former\Form;
use Umomega\Former\Answer;

class Surveyor {

	/**
	 * Validate and record
	 *
	 * @param Request $request
	 * @param int $formId
	 * @return Answer
	 */
	public function validateAndRecord(Request $request, $formId)
	{
		$validated = $request->validate(
			$this->getValidationRulesFor($formId)
		);

		$answer = new Answer();
		$answer->form_id = $formId;
		$answer->uuid = \Str::uuid();
		$answer->ip = $request->ip();
		$answer->status = 30;
		$answer->form_data = json_encode($validated);

		$answer->save();

		return $answer;
	}

	/**
	 * Returns the validation rules for the form
	 *
	 * @param int $formId
	 * @return array
	 */
	protected function getValidationRulesFor($formId)
	{
		return \Cache::rememberForever('form.' . $formId . '.rules', function() use ($formId) {

            $fieldsData = Form::findOrFail($formId)->fields()->orderBy('position')->get();

            $rules = [];

            foreach($fieldsData as $field)
            {
                if(!$field->is_visible) continue;

                $rules[$field->name] = (empty($field->rules) ? 'nullable' : $field->rules);
            }

            return $rules;
        });
	}

}