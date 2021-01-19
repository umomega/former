<?php

namespace Umomega\Former\Http\Controllers;

use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Former\Answer;
use Umomega\Former\Http\Requests\UpdateAnswer;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class AnswersController extends Controller
{

	/**
	 * Returns a list of answers
	 *
	 * @param Request $request
	 * @return json
	 */
	public function index(Request $request)
	{
		$answers = Answer::orderBy($request->get('s', 'created_at'), $request->get('d', 'desc'));

		if($request->get('f', 'all') != 'all') {
			$answers->where('status', $request->get('f'));
		}

		return $answers->paginate(15);
	}

	/**
	 * Returns a list of answers filtered by search
	 *
	 * @param Request $request
	 * @return json
	 */
	public function search(Request $request)
	{
		return ['data' => (new Search())
			->registerModel(Answer::class, 'uuid')
			->search($request->get('q'))
			->map(function($answer) {
				return $answer->searchable;
			})];
	}

	/**
	 * Retrieves the answer information
	 *
	 * @param Answer $answer
	 * @return json
	 */
	public function show(Answer $answer)
	{
		return $answer->liquify();
	}

	/**
	 * Updates the answer
	 *
	 * @param UpdateAnswer $request
	 * @param Answer $answer
	 * @return json
	 */
	public function update(UpdateAnswer $request, Answer $answer)
	{
		$answer->update($request->validated());

		activity()->on($answer)->log('AnswerUpdated');

		return [
			'message' => __('former::answers.edited'),
			'payload' => $answer
		];
	}

	/**
	 * Bulk deletes answers
	 *
	 * @param Request $request
	 * @return json
	 */
	public function destroyBulk(Request $request)
	{
		$items = $this->validate($request, ['items' => 'required|array'])['items'];
		
		$names = Answer::whereIn('id', $items)->pluck('uuid')->toArray();
		
		Answer::whereIn('id', $items)->delete();

		activity()->withProperties(compact('names'))->log('AnswersDestroyedBulk');

		return [
			'message' => __('former::answers.deleted_multiple')
		];
	}

	/**
	 * Deletes an answer
	 *
	 * @param Answer $answer
	 * @return json
	 */
	public function destroy(Answer $answer)
	{
		$name = $answer->uuid;

		$answer->delete();

		activity()->withProperties(compact('name'))->log('AnswerDestroyed');

		return [
			'message' => __('former::answers.deleted')
		];
	}

}