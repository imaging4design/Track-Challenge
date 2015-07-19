<?php

class Selection extends \Eloquent {

	protected $fillable = array('user_id', 'event_id', 'gender', 'athlete_id', 'position');

	public $timestamps = false;

	protected $guarded = array(); //Important - won't create new entry without this!


	/*
	|
	| Relationships
	|
	*/
	public function athletes() {
		return $this->hasMany('Athlete');
		//return $this->hasMany('Event');
		//return $this->hasOne('Eventname');
	}

	public function eventnames() {
		//return $this->hasMany('Event');
		return $this->hasOne('Eventname', 'id', 'event_id');
		//return $this->hasMany('Event');
		//return $this->hasOne('Eventname');
	}


}