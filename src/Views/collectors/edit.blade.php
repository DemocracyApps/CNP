@extends('layouts.default')

@section('content')
<h1>Edit Collector</h1>
{{ Form::open(['route' => array('collectors.update', $collector->id), 
               'method' => 'put', 'files' => true]) }}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', $collector->name, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('description', 'Description: ') }}
      {{ Form::textarea('description', $collector->description, ['class' => 'form-control']) }}
      <br/>
   </div>
   <div class="form-group">
      {{ Form::label('collector', 'Replace Specification')}}
      {{ Form::file('collector')}}
      <br/>
      <span class="error">{{ $errors->first('fileerror') }}</span>
   </div>
   <br/>
   <div class="form-group">
	  {{ Form::submit('Update Collector', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
