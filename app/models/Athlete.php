<?php

class Athlete extends \Eloquent {

	protected $fillable = array('event_id', 'rank', 'athleteName', 'country');

	public $timestamps = false;


	/*
	|
	| Relationships
	|
	*/
	public function eventnames() {
		return $this->belongsTo('Eventname');
		//return $this->hasMany('Event');
		//return $this->hasOne('Eventname');
	}

	public function selections() {
		return $this->hasMany('Selection');
	}

}