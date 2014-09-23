@extends('layouts.default')

@section('content')
<h1>Create New Project Template</h1>

{{ Form::open(['route' => 'composers.store', 'files' => true]) }}
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
      {{ Form::label('composer', 'Specification')}}
      {{ Form::file('composer')}}
      
      <span class="error">{{ $errors->first('fileerror') }}</span>
   </div>
   <br/>
   <div class="form-group">
	  {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
