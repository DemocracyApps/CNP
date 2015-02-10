@extends('templates.default')

@section('content')

    {{ Form::open(['route' => 'admin.perspectives.store', 'files' => true]) }}
        {{ Form::hidden('project', $project)}}

        <h1>New Perspective</h1>

        <br>
        <div class="form-group">
            {{ Form::label('name', 'Name: ') }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            <br>
            <span class="error">{{ $errors->first('name') }}</span>
            <br>
        </div>

        <div class="form-group">
            {{ Form::label('specification', 'Specification')}}
            {{ Form::file('specification')}}

            <span class="error">{{ $errors->first('fileerror') }}</span>
            <br>
        </div>

        <div class="form-group">
            {{ Form::label('description', 'Description: ') }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            <br>
        </div>

        <div class="form-group">
            {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
        </div>
    {{ Form::close() }}

@stop
