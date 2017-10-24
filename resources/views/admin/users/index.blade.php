@foreach($TestJSON_MyUsers as $user)
	<li>{{ $user['first_name'] }} {{ $user['last_name'] }} from {{ $user['location'] }}</li>
@endforeach

