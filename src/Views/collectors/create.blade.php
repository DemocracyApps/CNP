@extends('layouts.default')

@section('content')
<h1>Create New Collector</h1>

{{ Form::open(['route' => 'collectors.store', 'files' => true]) }}
   {{ Form::hidden('scape', $scape)}}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('description', 'Description: ') }}
      {{ Form::textarea('description', null, ['class' => 'form-control']) }}
      <br/>
   </div>
   <div class="form-group">
      {{ Form::label('collector', 'Specification')}}
      {{ Form::file('collector')}}
      
      <span class="error">{{ $errors->first('fileerror') }}</span>
   </div>
   <br/>
   <div class="form-group">
	  {{ Form::submit('Create Collector', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
