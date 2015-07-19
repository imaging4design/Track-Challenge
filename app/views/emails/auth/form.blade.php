<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">

		<link rel="stylesheet" type="text/css" href="../../dist/css/style.min.css">

	</head>
	<body>
		
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3">


					@if(isset($error))
						<p>{{$error}}</p>
					@endif
					

					@if(isset($user))

						{{ Form::open(array('url' => 'auth/setPassword')) }}
							<h1>Hi {{$user->screen_name}}</h1>
							<p>Reset your password below ...</p>
							<!-- <p>You registered on {{ date("d F Y",strtotime($user->created_at)) }}</p> -->
						

							{{ Form::token() }}
							{{ Form::hidden('remember_token', $user->remember_token) }}
							
							<div class="form-group">
								{{ Form::label('password', 'Password') }}
								{{ Form::password('password', array('class' => 'form-control input-lg')) }}
							</div>

							
							<div class="form-group">
								{{ Form::label('password_confirmation', 'Confirm Password') }}
								{{ Form::password('password_confirmation', array('class' => 'form-control input-lg')) }}
							</div>

							{{ Form::submit('Save', array('class' => 'btn btn-default btn-lg')) }}

						{{ Form::close() }}

					@endif


				</div><!--ENDS col-->
			</div><!--ENDS row-->
		</div><!--ENDS container-->

	</body>
</html>


 