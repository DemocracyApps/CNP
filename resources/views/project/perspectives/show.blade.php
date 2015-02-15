
@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-md-9">
            <h1>{!! $perspective->name !!}!</h1>
        </div>
        <div class="col-md-3">
            <a href="/{!! $project->id!!}/perspectives">All Perspectives</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
        <p> {!! $perspective->description !!} </p>
        </div>
    </div>
    <br>
    <br>
    <div class="row">

    </div>
        <div class="col-offset-2">
        <div class="col-md-8">
            {!! $perspective->getContent(); !!}

        </div>
    </div>
    <br>
@stop
