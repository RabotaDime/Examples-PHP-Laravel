<style>form { margin: 5px 0; border: 0px dashed lightgrey; }</style>
<style>input { margin: 10px 0; display: block; }</style>
<style>h3 { margin: 30px 0 5px 0; display: block; }</style>

<h1>Vehicle Routes</h1>
<p></p>

<h3>Upload CSV data file (Database Import)</h3>
<hr color="lightgrey" />
<div>
{!! Form::open(['route' => ['vehicles.routes.import'], 'files' => true, 'method' => 'post']) !!}
	{{ Form::file('datafile') }}
	{{ Form::submit() }}
{!! Form::close() !!}
<!--<form method="POST" action="/vehicles/routes/">
	{!! csrf_field() !!}
	<input name="datafile" type="file" />
	<input type="submit" />
</form>
-->
</div>


<h3>Preview Track</h3>
<hr color="lightgrey" />
<div>
</div>

