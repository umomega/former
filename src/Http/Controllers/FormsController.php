<?php

namespace Umomega\Former\Http\Controllers;

use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Former\Form;
use Umomega\Former\FormField;
use Umomega\Former\Http\Requests\StoreForm;
use Umomega\Former\Http\Requests\UpdateForm;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class FormsController extends Controller
{

	/**
	 * Returns a list of forms
	 *
	 * @param Request $request
	 * @return json
	 */
	public function index(Request $request)
	{
		return Form::orderBy($request->get('s', 'name'), $request->get('d', 'asc'))->paginate(15);
	}

	/**
	 * Returns a list of forms filtered by search
	 *
	 * @param Request $request
	 * @return json
	 */
	public function search(Request $request)
	{
		return ['data' => (new Search())
			->registerModel(Form::class, 'name')
			->search($request->get('q'))
			->map(function($form) {
				return $form->searchable;
			})];
	}

	/**
	 * Stores the new form
	 *
	 * @param StoreForm $request
	 * @return json
	 */
	public function store(StoreForm $request)
	{
		$form = Form::create($request->validated());

		activity()->on($form)->log('FormStored');

		return [
			'message' => __('former::forms.created'),
			'payload' => $form
		];
	}

	/**
	 * Retrieves the form information
	 *
	 * @param Form $form
	 * @return json
	 */
	public function show(Form $form)
	{
		return $form;
	}

	/**
	 * Retrieves the form fields
	 *
	 * @param Form $form
	 * @return json
	 */
	public function fields(Form $form)
	{
		return $form->fields()->orderBy('position')->get();
	}

	/**
	 * Sorts the form fields
	 *
	 * @param Request $request
	 * @param Form $form
	 * @return json
	 */
	public function sort(Request $request, Form $form)
	{
		$sorted = $request->get('sorted');

		$i = 1;

		foreach($sorted as $id)
		{
			FormField::where('id', $id)->update(['position' => $i]);
			$i++;
		}

		flush_form_schema_cache($form->id);

		return;
	}

	/**
	 * Updates the form
	 *
	 * @param UpdateForm $request
	 * @param Form $form
	 * @return json
	 */
	public function update(UpdateForm $request, Form $form)
	{
		$form->update($request->validated());

		activity()->on($form)->log('FormUpdated');

		return [
			'message' => __('former::forms.edited'),
			'payload' => $form
		];
	}

	/**
	 * Bulk deletes forms
	 *
	 * @param Request $request
	 * @return json
	 */
	public function destroyBulk(Request $request)
	{
		$items = $this->validate($request, ['items' => 'required|array'])['items'];
		
		$names = Form::whereIn('id', $items)->pluck('name')->toArray();
		
		Form::whereIn('id', $items)->delete();

		activity()->withProperties(compact('names'))->log('FormsDestroyedBulk');

		return [
			'message' => __('former::forms.deleted_multiple')
		];
	}

	/**
	 * Deletes a form
	 *
	 * @param Form $form
	 * @return json
	 */
	public function destroy(Form $form)
	{
		$name = $form->name;

		$form->delete();

		activity()->withProperties(compact('name'))->log('FormDestroyed');

		return [
			'message' => __('former::forms.deleted')
		];
	}

	/**
	 * Duplicates the form
	 *
	 * @param Form $form
	 * @return json
	 */
	public function duplicate(Form $form)
	{
		$clone = $form->duplicate();

		activity()->on($form)->log('FormDuplicated');

		return [
			'message' => __('former::forms.duplicated'),
			'payload' => $clone
		];
	}
}