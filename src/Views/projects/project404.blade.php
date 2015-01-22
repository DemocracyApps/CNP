
@extends('layouts.default_ext')

@section('title')
    No such project.
@stop

@section('content')
    <p> We were not able to find any project with the ID {{$project}}.</p>
    <div class="row">
        <div class=""col-xs-1"></div>
        <div class="col-xs-6">
            <button class="btn btn-success btn-sm" onclick="window.location.href='/'">Continue</button>
        </div>
    </div>


@stop
