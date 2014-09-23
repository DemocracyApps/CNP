@extends('layouts.default')

@section('content')
<h1>Create New Project View</h1>

{{ Form::open(['route' => 'vistas.store', 'files' => true]) }}
   {{ Form::hidden('scape', $scape)}}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <div class="form-group">
      {{ Form::label('input_composers', 'Allowed Input Template IDs: ') }}
      {{ Form::text('input_composers', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('input_composers') }}</span>
   </div>
   <div class="form-group">
      {{ Form::label('output_composer', 'Output Template ID: ') }}
      {{ Form::text('output_composer', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('output_composer') }}</span>
   </div>

   <div class="form-group">
      {{ Form::label('description', 'Description: ') }}
      {{ Form::textarea('description', null, ['class' => 'form-control']) }}
      <br/>
   </div>

   <div class="form-group">
      {{ Form::label('selector', 'Allowed Element Types: ') }}
      {{ Form::text('selector', null, ['class' => 'form-control']) }}      
   </div>
   <br/>
   <div class="form-group">
	  {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
