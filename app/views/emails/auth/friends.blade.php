<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Your friend {{$screen_name}} has challenged you to the World Track Challenge</h2>
		<p>See if you can out score your friend (xxxxx) when it comes to selecting the most medalists at the up-coming World Athletics Championships.</p>

		{{ url() }}

		<table>
			@foreach ($picks as $pick)
				@foreach ($pick as $row)
				<tr>
					<td><h3><strong>{{$row->eventName}} - ({{$row->gender}})</strong></h3></td>
				</tr>
				<tr>
					<!-- <td><img src="{{ url() }}/img/flags/{{$row->GoldFlag}}.png" width="20px" height="auto"> {{$row->Gold}}</td> -->
					<td><strong>1st</strong> {{$row->Gold}} - <strong>{{$row->GoldFlag}}</strong></td>
				</tr>
				<tr>
					<!-- <td><img src="{{ url() }}/img/flags/{{$row->SilverFlag}}.png" width="20px" height="auto"> {{$row->Silver}}</td> -->
					<td><strong>2nd</strong> {{$row->Silver}} - <strong>{{$row->SilverFlag}}</strong></td>
				</tr>
				<tr>
					<!-- <td><img src="{{ url() }}/img/flags/{{$row->BronzeFlag}}.png" width="20px" height="auto"> {{$row->Bronze}}</td> -->
					<td><strong>3rd</strong> {{$row->Bronze}} - <strong>{{$row->BronzeFlag}}</strong></td>
				</tr>
				@endforeach
			@endforeach
		</table>



	</body>
</html>
