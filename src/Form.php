<?php

namespace Umomega\Former;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Bkwld\Cloner\Cloneable;

class Form extends Model implements Searchable {

	use Cloneable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    /**
	 * Cloneable relations for duplication
	 *
	 * @var array
	 */
	protected $cloneable_relations = ['fields'];

	/**
	 * Searchable config
	 *
	 * @return SearchResult
	 */
	public function getSearchResult(): SearchResult
	{
		return new SearchResult($this, $this->name);
	}

	/**
	 * Content Field relation
	 *
	 * @return HasMany
	 */
	public function fields()
	{
		return $this->hasMany(FormField::class);
	}

	/**
	 * Answers relation
	 *
	 * @return HasMany
	 */
	public function answers()
	{
		return $this->hasMany(Answer::class);
	}

	/**
	 * Modifier for duplication
	 *
	 * @param $source
	 * @param $child
	 */
	public function onCloning($source, $child)
	{
		$this->name .= ' [' . __('foundation::general.copy') . ']';
	}

}