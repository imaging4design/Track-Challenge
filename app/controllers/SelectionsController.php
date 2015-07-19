<?php

class SelectionsController extends \BaseController {

	// Run beforeFilter prior to ALL functions in this controller
	public function __construct()
    {
        $this->beforeFilter('auth');
    }


    /*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: find_user_picks(userID, eventID, gender)
	| Used to check if any picks have already been made for this event by this user!
	| Example: Is there a record in the database containing the posted userID and eventID?
	| If so, send json response 'found_user_pick' => true
	| Id not, send json response 'found_user_pick' => false
	| This is evaluated by the $scope.check function() in the 'SelectCtrl' of 'mainCtrl'
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function find_user_picks($user_id, $event_id, $gender)
	{
		$user_picks = Selection::where('user_id', '=', $user_id)->where('event_id', '=', $event_id)->where('gender', '=', $gender)->get();

		foreach ($user_picks as $row) {
			$user_pick[] = array(
				'id' => $row->id,
				'user_id' => $row->user_id,
				'event_id' => $row->event_id,
				'gender' => $row->gender,
				'athlete_id' => $row->athlete_id,
				'gender' => $row->gender
			);
		}

		//return Response::json($user_pick);
		if( ! $user_picks->isEmpty()) {
			return Response::json(array('found_user_pick' => true, 'user_pick' => $user_pick));
		} 
		else {
			return Response::json(array('found_user_pick' => false));
		}
		
	}




	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: find_all_user_picks(id)
	| Used to display a list of all the user picks to date (i.e., Gold, Silver, Bronze) for each event - both men & women
	| Returns a json array object of data (result.all_user_pick) back to SelectCtrl of mainCtrl
	|-----------------------------------------------------------------------------------------------------------------
	*/
	// This used to display lists of selections previously made
	public function find_all_user_picks($id, $gender)
	{

		$all_user_picks = Selection::where('user_id', '=', $id)->where('gender', '=', $gender)->groupBy('event_id')->get();

		// Get all the user picks data and store in $all_user_pick[]
		foreach ($all_user_picks as $row) {

			$all_user_pick[] = DB::select( DB::raw("
				SELECT selections.id, selections.event_id, selections.position, eventnames.eventName, UPPER(athletes.name_last) as name_last
				FROM selections
				LEFT JOIN athletes ON athletes.id = selections.athlete_id
				LEFT JOIN eventnames ON eventnames.id = selections.event_id
				WHERE selections.user_id = ?
				AND selections.gender = ?
				AND selections.event_id = ?
				ORDER BY eventnames.id ASC, selections.position ASC
			"), array($id, $gender, $row->event_id)); // Escape values being passed in ...

		}

		// Loop through each 'event' and pull in the positions
		foreach ($all_user_pick as $row) {
			
			$my_picks[] = array(
				'event_id' => $row[0]->event_id,
				'eventName' => $row[0]->eventName,
				'gold' => $row[0]->name_last,
				'silver' => $row[1]->name_last,
				'bronze' => $row[2]->name_last
			);
		}


		// Return Response::json()
		if( $all_user_pick ) {
			return Response::json(array('success' => true, 'my_picks' => $my_picks));
		} else {
			return Response::json(array('success' => false));
		}
		
	}




	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: email_picks(id)
	| Used to send a list of users picks to an email recipient - encouraging friend to challenge
	| Returns a json array object of data (result.email_picks) back to SelectCtrl of mainCtrl
	|-----------------------------------------------------------------------------------------------------------------
	*/
	// This used to display lists of selections previously made
	public function email_picks($id, $email)
	{

		$all_events = Selection::where('user_id', '=', $id)->groupBy('user_id')->get();

		foreach ($all_events as $row) {

			$eventID = $row->event_id;

			//SELECT selections.id, selections.event_id, eventnames.eventName, CONCAT(ath.name_first, ' ', UPPER(ath.name_last)) as Gold,  CONCAT(ath2.name_first, ' ', UPPER(ath2.name_last)) as Silver,  CONCAT(ath3.name_first, ' ', UPPER(ath3.name_last)) as Bronze
			
			$email_picks[] = DB::select( DB::raw("
				SELECT selections.id, selections.event_id, UPPER(selections.gender) as gender, eventnames.eventName, 
					CONCAT(ath.name_first, ' ', UPPER(ath.name_last)) as Gold,
					CONCAT(ath2.name_first, ' ', UPPER(ath2.name_last)) as Silver,
					CONCAT(ath3.name_first, ' ', UPPER(ath3.name_last)) as Bronze,
					ath.country as GoldFlag,
					ath2.country as SilverFlag,
					ath3.country as BronzeFlag
				FROM selections
				LEFT JOIN athletes ath ON gold = ath.id
				LEFT JOIN athletes ath2 ON silver = ath2.id
				LEFT JOIN athletes ath3 ON bronze = ath3.id
				LEFT JOIN eventnames ON eventnames.id = selections.event_id
				WHERE selections.user_id = ?
				
				ORDER BY eventnames.id ASC, selections.gender ASC
			"), array($id)); // Escape values being passed in ...

		}

		// Sends email to users 'friend' containing their picks
		if( $email_picks) {

			Mail::send('emails.auth.friends', array('screen_name' => 'Gavin Lovegrove', 'picks' => $email_picks, 'link' => URL::to('/')), function($message) use ($email_picks, $email)  { 
				$message->to($email, 'Gavin Lovegrove')->subject('Welcome!');
			});

			//return Response::json(array('success' => false));
			return Response::json(array('flash' => 'Email successfully sent!', 'email_picks' => $email_picks));

		}
		
	} // ENDS email_picks($id)



	



	/**
	 * Display a listing of the resource.
	 * GET /regions
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /regions/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /regions
	 *
	 * @return Response
	 */
	public function store()
	{

		// UPDATE EXISTING ...
		// First find out if the user has existing selections for this event
		$id = Input::get('id');
		$existing = Selection::find($id);

		// If ($existing), update the record for the event
		if($existing != null) {

			$gold = Selection::find(Input::get('id'));
			$gold->athlete_id = Input::get('gold');
			$gold->save();

			$silver = Selection::find(Input::get('id2'));
			$silver->athlete_id = Input::get('silver');
			$silver->save();

			$bronze = Selection::find(Input::get('id3'));
			$bronze->athlete_id = Input::get('bronze');
			$bronze->save();

		} else {

			// INSERT NEW ...
			// If ( ! $existing), then create a new entry
			$gold = Selection::create( array( 
				'user_id' => Input::get('user_id'),
				'event_id' => Input::get('event_id'),
				'gender' => Input::get('gender'),
				'athlete_id' => Input::get('gold'),
				'position' => 1
			));
			$silver = Selection::create( array( 
				'user_id' => Input::get('user_id'),
				'event_id' => Input::get('event_id'),
				'gender' => Input::get('gender'),
				'athlete_id' => Input::get('silver'),
				'position' => 2
			));
			$bronze = Selection::create( array( 
				'user_id' => Input::get('user_id'),
				'event_id' => Input::get('event_id'),
				'gender' => Input::get('gender'),
				'athlete_id' => Input::get('bronze'),
				'position' => 3
			));

			return Response::json(array(
				'Saved Picks' => true, 'id' => $id
			));

		}

	}

	/**
	 * Display the specified resource.
	 * GET /regions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */


	public function show($id)
	{
		// 
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /regions/{id}/edit
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
	 * PUT /regions/{id}
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
	 * DELETE /regions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}