
@extends('templates.default')

@section('content')
    <div class="row">
        <h1>{!! $perspective->name !!}!</h1>
        <p> {!! $perspective->description !!} </p>
        <br>
    </div>
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
