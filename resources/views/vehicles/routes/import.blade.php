@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			@if (Auth::check())
	            <div class="panel panel-default">
    	            <div class="panel-heading"><h2><a href="/">Waypoints API</a><span class="ViewTitleDelimiter">/</span>Импорт данных для машины ({{ $Vehicle->ID or  '--' }})</h2></div>
        	        <div class="panel-body">

					@if (isset($ImportMethod) && ($ImportMethod === 'direct'))
						<h4>Прямая загрузка CSV-файла</h4>
					@elseif (isset($ImportMethod) && ($ImportMethod === 'debug'))
						<h4>Отладочная загрузка CSV-файла</h4>
					@else
						<h4>Отложенная загрузка CSV-файла с динамическим показом процесса обработки</h4>
					@endif
					
					
					<div class="MyImportForm">
					<hr color="lightgrey" />
					{!! Form::open(['route' => ['api.vehicles.routes.import', $Vehicle->ID], 'files' => true, 'method' => 'post']) !!}
						{{ Form::hidden('vehicle_id', $Vehicle->ID) }}
						{{ Form::file('datafile') }}
						{{ Form::submit('Начать импорт CSV-данных') }}
					{!! Form::close() !!}
					<!--<form method="POST" action="/vehicles/routes/">
						{!! csrf_field() !!}
						<input name="datafile" type="file" />
						<input type="submit" />
					</form>
					-->
					</div>
										


	                </div>
	            </div>
            @else
	            <div class="panel panel-default">
    	            <div class="panel-heading"><h2>Waypoints API</h2></div>
        	        <div class="panel-body">
                    	<p>You are not authorized. Please use the <a href="/login">Login</a> form.</p>
	                </div>
	            </div>
			@endif
        </div>
    </div>
</div>
@endsection

