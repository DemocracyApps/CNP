
@extends('templates.default')

@section('title')
    Successfully authorized!
@stop

@section('content')
    <p> Welcome to  {{ $project->name }}!</p>
    <div class="row">
        <div class=""col-xs-1"></div>
        <div class="col-xs-6">
            <button class="btn btn-success btn-sm" onclick="window.location.href='/{{$project->id}}/authorized'">Continue</button>
        </div>
    </div>


@stop
