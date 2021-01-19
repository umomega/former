<?php

namespace Umomega\Former\Http\Controllers;

use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Former\FormField;
use Umomega\Former\Form;
use Umomega\Former\Http\Requests\StoreFormField;
use Umomega\Former\Http\Requests\UpdateFormField;
use Illuminate\Http\Request;

class FormFieldsController extends Controller
{

	/**
	 * Stores the new form field
	 *
	 * @param StoreForm $request
	 * @param Form $form
	 * @return json
	 */
	public function store(StoreFormField $request, Form $form)
	{
		$formField = new FormField($request->validated());
		$formField->position = $form->fields()->count() + 1;

		$form->fields()->save($formField);

		flush_form_schema_cache($form->id);

		activity()->on($formField)->log('FormFieldStored');

		return [
			'message' => __('former::fields.created'),
			'payload' => $formField
		];
	}

	/**
	 * Retrieves the form field information
	 *
	 * @param Form $form
	 * @param FormField $formField
	 * @return json
	 */
	public function show(Form $form, FormField $formField)
	{
		$formField->form = $form;

		return $formField;
	}

	/**
	 * Updates the form field
	 *
	 * @param UpdateFormField $request
	 * @param Form $form
	 * @param FormField $formField
	 * @return json
	 */
	public function update(UpdateFormField $request, Form $form, FormField $formField)
	{
		$formField->update($request->validated());
		$formField->form = $form;

		flush_form_schema_cache($form->id);

		activity()->on($formField)->log('FormFieldUpdated');

		return [
			'message' => __('former::fields.edited'),
			'payload' => $formField
		];
	}

	/**
	 * Deletes a form field
	 *
	 * @param int $form
	 * @param FormField $formField
	 * @return json
	 */
	public function destroy($form, FormField $formField)
	{
		$name = $formField->name;

		$formField->delete();

		flush_form_schema_cache($form);

		activity()->withProperties(compact('name'))->log('FormFieldDestroyed');

		return ['message' => __('former::fields.deleted')];
	}

}