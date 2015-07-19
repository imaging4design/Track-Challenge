<?php

class StatsController extends \BaseController {

	// Run beforeFilter prior to ALL functions in this controller
	public function __construct()
	{
		$this->beforeFilter('auth');
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
	public function show($event_id, $gender)
	{
		
		// Get (count()) num of times an athlete has been selected for each event
		$all_user_pick[] = DB::select( DB::raw("
			SELECT COUNT(*) as num, selections.id, selections.event_id, selections.position, eventnames.eventName, UPPER(athletes.name_last) as name_last, athletes.name_first
			FROM selections
			LEFT JOIN athletes ON athletes.id = selections.athlete_id
			LEFT JOIN eventnames ON eventnames.id = selections.event_id
			WHERE selections.event_id = ?
			AND selections.gender = ?
			GROUP BY selections.athlete_id
			ORDER BY num DESC
		"), array($event_id, $gender)); // Escape values being passed in ...
		

		// Initiate var
		$results = [];

		foreach ($all_user_pick as $row) {
			foreach ($row as $r) {
				$results[] = array(
					'num' => $r->num,
					'name_last' => $r->name_last,
					'name_first' => $r->name_first
				);
			}
		}
		

		return Response::json(array('success' => true, 'all_user_pick' => $results));
		

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