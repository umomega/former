<?php

namespace Umomega\Former;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model {

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'label', 'description', 'position', 'type', 'is_visible',
        'default_value', 'rules', 'options',
    ];

	/**
     * Form relation
     *
     * @return BelongsTo
     */
    public function form()
    {
    	return $this->belongsTo(Form::class);
    }

}