@extends('layouts.default')

@section('content')
<h1>Create New Vista</h1>

{{ Form::open(['route' => 'vistas.store', 'files' => true]) }}
   {{ Form::hidden('scape', $scape)}}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>

   <div class="form-group">
      {{ Form::label('description', 'Description: ') }}
      {{ Form::textarea('description', null, ['class' => 'form-control']) }}
      <br/>
   </div>

   <div class="form-group">
      {{ Form::label('topelements', 'Allowed Element Types: ') }}
      {{ Form::text('topelements', null, ['class' => 'form-control']) }}      
   </div>
   <br/>
   <div class="form-group">
	  {{ Form::submit('Create Vista', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
