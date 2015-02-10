@extends('layouts.default')

@section('content')
<h1>Edit Composer</h1>
{{ Form::open(['route' => array('admin.composers.update', $composer->id), 
               'method' => 'put', 'files' => true]) }}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', $composer->name, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('description', 'Description: ') }}
      {{ Form::textarea('description', $composer->description, ['class' => 'form-control']) }}
      <br/>
   </div>
   <div class="form-group">
      {{ Form::label('output', 'Preferred Output Composer: ') }}
      {{ Form::text('output', $composer->output, ['class' => 'form-control']) }}
      <br/>
   </div>
   <div class="form-group">
      {{ Form::label('composer', 'Replace Specification')}}
      {{ Form::file('composer')}}
      <br/>
      <span class="error">{{ $errors->first('fileerror') }}</span>
   </div>
   <br/>
   <div class="form-group">
	  {{ Form::submit('Update Composer', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
