@extends('layouts.default')

@section('content')
<h1>Create New Scape</h1>

{{ Form::open(['route' => 'scapes.store']) }}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('access', 'Access: ') }}

      <select id="access" name="access" class="form-control">
          <option value="Open">Open</option>
          <option value="Closed">Closed</option>
          <option value="Private">Private</option>
      </select>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('content', 'Notes: ') }}
      {{ Form::textarea('content', null, ['class' => 'form-control']) }}
      <br/>
   </div>
   <div>
	{{ Form::submit('Create', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
