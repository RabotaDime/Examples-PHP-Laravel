@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			@if (Auth::check())
	            <div class="panel panel-default">
    	            <div class="panel-heading"><h2>Waypoints API<span class="ViewTitleDelimiter">/</span>Все машины ({{ $VehiclesCount or  '--' }})</h2></div>
        	        <div class="panel-body">

						<p>Список машин, с подсчетом кол-ва сохраненных путевых историй:</p>

        	        	<!-- <p><pre>@php //var_dump($Vehicles); @endphp</pre></p> -->

        	        	<table class="table table-striped table-bordered">
        	        		<thead>
        	        			<tr>
        	        				<th>#</th>
        	        				<th>Марка (Модель)</th>
        	        				<th>Одна из путевых историй</th>
        	        				<th>Все путевые записи</th>
        	        				<th>Операции</th>
        	        			</tr>
        	        		</thead>
        	        		<tbody>
        	        		@foreach($Vehicles as $Vehicle)
        	        			<tr>
        	        				<td>{{ $Vehicle->VehicleID or "Error" }}</td>
        	        				<td>{{ $Vehicle->VehicleBrand or "BrandError" }} (<b>{{ $Vehicle->VehicleModel or "ModelError" }}</b>)</td>
        	        				<td><a href="{{ route('vehicle_route', [$Vehicle->VehicleID, $Vehicle->FirstRouteID]) }}/">Открыть путь #{{ $Vehicle->FirstRouteID or "ErrorFirstRouteID" }}</a></td>
        	        				<td><a href="{{ route('vehicle_routes', [$Vehicle->VehicleID]) }}/">Все пути ({{ $Vehicle->TotalRoutes or "--" }})</a>
        	        				@php
        	        					//echo '<pre>';var_dump($Vehicle);echo '</pre>';
									@endphp
									</td>
									<td><a href="{{ route('vehicle_route_import', [$Vehicle->VehicleID]) }}/"><button class="btn btn-xs btn-primary">Импорт</button></a></td>
        	        			</tr>
        	        		@endforeach
        	        		</tbody>
        	        	</table>
<!-- 
						<div class="col-md-6 col-md-offset-3">
							<ul class="list-group">
							@foreach ($Vehicles as $Vehicle)
								<li class="list-group-item">
									<span>
										{{ $Vehicle->VehicleID or "Error" }}
									</span>
									<span>
										{{ $Vehicle->Description or "Error" }}
									</span>
									<span class="pull-right clearfix">
										Text 2
									
										
									</span>
								</li>
							@endforeach
							</ul>
						
							{{ $Vehicles->links() }}
						</div>
-->
<!-- 
                        <div class="alert alert-success">
                            {!! session('status') !!}
                        </div>
 -->
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

