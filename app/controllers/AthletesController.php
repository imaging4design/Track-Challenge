<?php

class AthletesController extends \BaseController {

	// Run beforeFilter prior to ALL functions in this controller
	public function __construct()
	{
		$this->beforeFilter('auth');
	}



	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: completed($id, $gender, $user)
	| Used to check if the user has completed their picks for this event
	| If so, display a message on that event saying ... 'you have already made selections for this event'!
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function completed($id, $gender, $user)
	{
		$my_picks = Selection::where('event_id', '=', $id)->where('gender', '=', $gender)->where('user_id', '=', $user)->get();
		
		if( ! $my_picks->isEmpty()) {
			return Response::json(array('found_user_pick' => true));
		} 
		else {
			return Response::json(array('found_user_pick' => false));
		}
		
	}



	/**
	 * Display a listing of the resource.
	 * GET /events
	 *
	 * @return Response
	 */
	public function index()
	{
		//return Response::json(array('Hello from the index' => true));
	}



	/**
	 * Show the form for creating a new resource.
	 * GET /events/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}



	/**
	 * Store a newly created resource in storage.
	 * POST /events
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}



	/**
	 * Display the specified resource.
	 * GET /events/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $gender)
	{
		$event = Eventname::find($id);
		$eventDropDowns = Eventname::all();
		$athletes = Athlete::where('event_id', '=', $id)->where('gender', '=', $gender)->orderBy('rank', 'asc')->take(20)->get();
		

		// Info for the specific event the user is currently interacting with
		$eventName[] = array(
			'eventID' => $event->id,
			'eventName' => $event->eventName, 
			'eventOrder' => $event->eventOrder,
			'eventImage' => $event->eventImage
		);

		
		// General event info used for the Drop Down events menu
		foreach ($eventDropDowns as $row) {
			$eventDropDown[] = array(
				'event_list_id' => $row->id,
				'event_list_name' => $row->eventName
			);
		}

		
		// Gets ALL athletes for a specific eventID and gender
		foreach ($athletes as $row) {
			$athlete[] = array(
				'id' => $row->id,
				'name_first' => $row->name_first,
				'name_last' => $row->name_last,
				'rank' => $row->rank,
				'country' => $row->country
			);
		}




		//$all_user_picks = Selection::where('user_id', '=', $id)->where('gender', '=', $gender)->groupBy('event_id')->get();

		// Get all the user picks data and store in $all_user_pick[]

		$expert[] = DB::select( DB::raw("
			SELECT users.screen_name, selections.id, selections.event_id, eventnames.eventName, selections.position, UPPER(athletes.name_last) as name_last
			FROM selections
			LEFT JOIN athletes ON athletes.id = selections.athlete_id
			LEFT JOIN eventnames ON eventnames.id = selections.event_id
			LEFT JOIN users ON users.id = selections.user_id
			WHERE selections.user_id IN (3,4,5)
			AND selections.event_id = ?
			AND selections.gender = ?
		"), array($id, $gender)); // Escape values being passed in ...


		// // Loop through each 'event' and pull in the positions
		foreach ($expert as $row) {
			foreach ($row as $r) {
				$expert_pick[] = array(
					'event_id' => $r->event_id,
					'eventName' => $r->eventName,
					'position1' => $r->name_last,
					'position2' => $r->name_last,
					'position3' => $r->name_last,
					'user' => $r->screen_name
				);
			}
		}

		//SELECT selections.id, selections.event_id, eventnames.eventName, CONCAT(ath.name_first, ' ', UPPER(ath.name_last)) as Gold,  CONCAT(ath2.name_first, ' ', UPPER(ath2.name_last)) as Silver,  CONCAT(ath3.name_first, ' ', UPPER(ath3.name_last)) as Bronze

		// Gets the EXPERTS Picks
		// $expert_pick[] = DB::select( DB::raw("
		// 	SELECT users.screen_name, selections.id, selections.event_id, eventnames.eventName, 
		// 		ath.country as gold_country , ath2.country as silver_country, ath3.country as bronze_country, 
		// 		CONCAT(UPPER(ath.name_first), ' ', ath.name_last) as Gold,  
		// 		CONCAT(UPPER(ath2.name_first), ' ', ath2.name_last) as Silver,  
		// 		CONCAT(UPPER(ath3.name_first), ' ', ath3.name_last) as Bronze
		// 	FROM selections
		// 	LEFT JOIN athletes ath ON gold = ath.id
		// 	LEFT JOIN athletes ath2 ON silver = ath2.id
		// 	LEFT JOIN athletes ath3 ON bronze = ath3.id
		// 	LEFT JOIN eventnames ON eventnames.id = selections.event_id
		// 	LEFT JOIN users ON users.id = selections.user_id
		// 	WHERE selections.user_id IN (3,4,5) 
		// 	AND selections.event_id = '$id'
		// 	AND selections.gender = '$gender'
		// 	ORDER BY selections.user_id ASC
		// ") );




		// if( isset( $athlete ) && isset( $expert_pick )) {
		// 	return Response::json(array('athlete' => $athlete, 'eventName' => $eventName, 'eventDropDown' => $eventDropDown, 'expert_picks' => $expert_pick));
		// 	//return Response::json(array('success' => true));
		// } else {
		// 	return Response::json(array('athlete' => 'no data', 'eventName' => $eventName, 'eventDropDown' => $eventDropDown, 'expert_picks' => $expert_pick));
		// }

		if( isset( $athlete )) {
			return Response::json(array('athlete' => $athlete, 'eventName' => $eventName, 'eventDropDown' => $eventDropDown));
			//return Response::json(array('success' => true));
		} else {
			return Response::json(array('athlete' => 'no data', 'eventName' => $eventName, 'eventDropDown' => $eventDropDown));
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /events/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}



	/**
	 * Update the specified resource in storage.
	 * PUT /events/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}



	/**
	 * Remove the specified resource from storage.
	 * DELETE /events/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}