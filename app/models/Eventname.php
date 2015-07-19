<?php

class Eventname extends \Eloquent {

	protected $fillable = array('id', 'eventOrder', 'eventName');

	public $timestamps = false;

	/*
	|
	| Relationships
	|
	*/
	public function athletes() {
		return $this->belongsTo('Athlete');
		//return $this->hasMany('Athlete');
		//return $this->belongsToMany('Athlete');
	}

	public function selections() {
		//return $this->belongsTo('Selection');
		return $this->belongsTo('Selection', 'event_id', 'id');
		//return $this->hasMany('Athlete');
		//return $this->belongsToMany('Athlete');
	}

}