
@extends('templates.default')

@section('title')
    Unable To Authorize - {{ $project->name }}
@stop

@section('content')
    <p> We are sorry, but we were not able to authorize you for this project. </p>
    <div class="row">
        <div class=""col-xs-1"></div>
        <div class="col-xs-6">
            <button class="btn btn-success btn-sm" onclick="window.location.href='/'">Home</button>
        </div>
    </div>


@stop
