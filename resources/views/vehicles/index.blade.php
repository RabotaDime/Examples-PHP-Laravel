@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h2>Waypoints API<span class="ViewTitleDelimiter">/</span>Привет</h2></div>
                <div class="panel-body">
                    @if (Auth::check())
                    
<!-- 
                        <div class="alert alert-success">
                            {!! session('status') !!}
                        </div>
 -->
                    @else
                    	<p>You are not authorized. Please use the Login form.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
