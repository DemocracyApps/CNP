@extends('layouts.default')

@section('content')

    {{ Form::open(['route' => array('admin.analysis.update', $analysis->id),
                    'method' => 'put', 'files' => true]) }}
        {{ Form::hidden('project', $analysis->project)}}

        <h1>New Analysis</h1>

        <br>
        <div class="form-group">
            {{ Form::label('name', 'Name: ') }}
            {{ Form::text('name', $analysis->name, ['class' => 'form-control']) }}
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
            {{ Form::label('notes', 'Notes: ') }}
            {{ Form::textarea('notes', $analysis->notes, ['class' => 'form-control']) }}
            <br>
        </div>

        <div class="form-group">
            {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
        </div>
    {{ Form::close() }}

@stop
