<?php

class AuthController extends \BaseController {


	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: login()
	| User login function
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function login() {

		// Set up 'Gravatar' data ....
		$email = Input::json('login_email');
		$default = "http://www.worldtrackchallenge.com/dist/img/gravatar.jpg";
		$size = 50;
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;


		if(Auth::attempt(array('email' => Input::json('login_email'), 'password' => Input::json('login_password')))) {
			return Response::json(array(Auth::user(), $grav_url));
		} else {
			return Response::json(array('flash' => 'Invalid username or password!'), 500);
		}
	
	} // ENDS login()




	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: logout()
	| User logout function
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function logout() {
		Auth::logout();
		return Response::json(array('flash' => 'Logged out!'));

	} // ENDS logout()




	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: register()
	| Register New User function
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function register() {

		//return Response::json(Input::all());

		// Custom error messages via $messages
		$messages = array(
			'required' => 'The :attribute field is required.',
			'unique' => 'Email address already taken ...'
		);

		$rules = array(
			'reg_email' => 'required|email|unique:users,email,id',
			'reg_password' => 'required|min:6',
			'reg_screen_name' => 'required|min:3',
			'csrf_token' => 'required'
		);

		// RUN VALIDATION
		// Inject 'custom error messages' - $messages into Validator::()
		$validator = Validator::make(Input::all(), $rules, $messages);
		//$validator = Validator::make($input, $rules, $messages);

		// Loop through errors
		$error_messages = $validator->errors()->getMessages();
		foreach ($error_messages as $row) {
			$error_message[] = $row;
		}

		


		if ($validator->fails())
		{
		    // Return error
		    return Response::json(array('flash' => $error_message), 500);
		    //return Response::json(array('flash' => 'Gav start validating'), 500);
		}
		else 
		{

			// INSERT NEW USER...
			$new_user = User::create(array(
				'email' => Input::get('reg_email'),
				'password' => Hash::make(Input::get('reg_password')),
				'screen_name' => Input::get('reg_screen_name'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			));


			// Gets the newly created user 'id' ...
			$insertId = $new_user->id;


			// Now Immediately login the 'newly created user' with Auth::attempt() ...
			$credentials = array(
				'email' => Input::get('reg_email'),
				'password' => Input::get('reg_password')
			);

			if (Auth::attempt($credentials)) {
				return Response::json(array(
					'insertId' => $insertId,
					'screen_name' => $new_user->screen_name,
					'user' => Auth::user()
				));
			}


		} // ENDS else


	} // ENDS register()








	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: resetPassword()
	| Allow the user to reset their password via email request
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function resetPassword() {

		// Custom error messages via $messages
		$messages = array(
			'required' => 'The :attribute field is required.'
		);

		$rules = array(
			'email' => 'required|email',
			'csrf_token' => 'required'
		);

		// RUN VALIDATION
		// Inject 'custom error messages' - $messages into Validator::()
		$validator = Validator::make(Input::all(), $rules, $messages);

		// Loop through errors
		$error_messages = $validator->errors()->getMessages();
		foreach ($error_messages as $row) {
			$error_message[] = $row;
		}

		if ($validator->fails())
		{
		    // Return error
		    return Response::json(array('flash' => $error_message), 500);
		}
		else 
		{

			// Check to see if user exists ...
			$user = [];
			$user = User::where('email', '=', Input::get('email'));

			if( $user->count() ) {

				$user = $user->first();

				// Create a $code (based on the remember_token) and generated $password for the user to be emailed ...
				$code = $user->remember_token;
				$password = str_random(10);

				$user->password_temp = Hash::make($password); // Hash the password
				
				if( $user->save() ) {

					// Email user and when they respond to the (sent) link - send them to the 'auth/recover' Route
					Mail::send('emails.auth.forgot', array('screen_name' => $user->screen_name, 'password' => $password, 'link' => URL::to('auth/recover', $code)), function($message)  { 
						$message->to('gavin@imaging4design.co.nz', 'Gavin Lovegrove')->subject('Welcome!');
					});

				}

				return Response::json(array('flash' => 'Email with new password sent ...'), 200);

			} else {

				return Response::json(array('flash' => 'Sorry, we don\'t have anyone with that email'), 500);

			}


		} // ENDS else
		
		
	} // ENDS resetPassword()



	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: getRecover($code)
	| Sends the user to the URL below containing a form to reset their password
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function getRecover($code) {

		$user = [];
		$user = User::where('remember_token', '=', $code);

		if( $user->count() ) {
			$user = $user->first();
			return View::make('emails/auth/form')->with('user', $user);
		} else {
			return View::make('emails/auth/form')->with('error', 'Sorry, this password reset option has expired. <br> Please try again. <a href=" ' . url() . ' "><strong>Here</strong></a>');
		}

		

	} // ENDS getRecover($code)



	/*
	|-----------------------------------------------------------------------------------------------------------------
	| NAME :: setPassword()
	| Posts new 'password' credentials from getRecover($code) form
	|-----------------------------------------------------------------------------------------------------------------
	*/
	public function setPassword() {

		// Custom error messages via $messages
		$messages = array(
			'required' => 'The :attribute field is required.'
		);

		$rules = array(
			'remember_token' => 'required',
			'password' => 'required|min:6',
			'password_confirmation' => 'required|same:password',
			'_token' => 'required'
		);

		// RUN VALIDATION
		// Inject 'custom error messages' - $messages into Validator::()
		$validator = Validator::make(Input::all(), $rules, $messages);


		// Loop through errors
		$error_messages = $validator->errors()->getMessages();
		foreach ($error_messages as $row) {
			$error_message[] = $row;
		}


		if ($validator->fails())
		{
		    // Return error
		    return Response::json(array('Password Message' => $error_message), 500);
		}
		else 
		{
			// UPDATE USER PASSWORD ...
			$user = User::where('remember_token', '=', Input::get('remember_token'))->find(1);
			$user->password = Hash::make(Input::get('password'));
			$user->save();

			// Redirect to Home page
			return Redirect::to('/')->with('flash', 'Success!');
		}

			
	
	} // ENDS setPassword()



} // ENDS controller 