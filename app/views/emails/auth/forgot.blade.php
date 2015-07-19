<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Password Reset</h2>

		<div>
			Hello {{ $screen_name }}, <br><br>
			Your new password is: {{ $password }} <br><br>
			----<br>
			{{ $link }} <br>
			----
		</div>
	</body>
</html>
