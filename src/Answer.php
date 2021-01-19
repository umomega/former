<?php

namespace Umomega\Former;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Answer extends Model implements Searchable {

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uuid', 'ip', 'status', 'notes', 'form_data'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['form_data' => 'array'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['form'];

    /**
	 * Searchable config
	 *
	 * @return SearchResult
	 */
	public function getSearchResult(): SearchResult
	{
		return new SearchResult($this, $this->uuid);
	}

	/**
     * Form relation
     *
     * @return BelongsTo
     */
    public function form()
    {
    	return $this->belongsTo(Form::class);
    }

    /**
     * Liquifies the form form_data into model and attaches the schema
     *
     * @return $this
     */
    public function liquify()
    {
        $this->schema = $this->getSchema();

        foreach($this->form_data as $key => $value)
        {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Returns the schema for the content's type
     *
     * @return array
     */
    public function getSchema()
    {
        if(!isset($this->attributes['form_id'])) return ['fields' => []];

        $formId = $this->attributes['form_id'];

        return \Cache::rememberForever('form.' . $formId, function() use ($formId) {

            $fieldsData = Form::findOrFail($formId)->fields()->orderBy('position')->get();

            $fields = [];
            $schema = [];

            foreach($fieldsData as $field)
            {
                $fields[$field->name] = ['type' => $field->type, 'field_id' => $field->id];

                if(!$field->is_visible) continue;

                $options = json_decode($field->options, true);

                $schema[] = [
                    'type' => ($field->type == 'ContentRelationField' ? 'RelationField' : $field->type),
                    'name' => $field->name,
                    'label' => $field->label,
                    'options' => ($field->type == 'ContentRelationField'
                        ? (is_array($options)
                            ? array_merge(['searchroute' => 'contents/search/relatable', 'namekey' => 'title', 'translated' => true, 'multiple' => true], $options)
                            : ['searchroute' => 'contents/search/relatable', 'namekey' => 'title', 'translated' => true, 'multiple' => true])
                        : $options),
                    'default_value' => $field->default_value,
                    'hint' => $field->description
                ];
            }

            return compact('fields', 'schema');
        });
    }

    /**
     * Getter for the form
     *
     * @return ContentType
     */
    public function getFormAttribute()
    {
        return $this->form()->first();
    }

}