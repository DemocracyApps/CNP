@extends('layouts.default')

@section('content')
<h1>Edit User</h1>

{{ Form::open(['route' => array($putUrl, $user->id), 
               'method' => 'put', 'files' => true]) }}

   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', $user->name, ['class' => 'form-control']) }}
      <br>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br>
   <div class="form-group">
      {{ Form::label('projectcreator', "Project Creator?") }}
      {{ Form::select('projectcreator', 
                      array('1' => 'Yes', '0' => 'No'), $user->projectcreator?'1':'0') }}
   </div>
   <br>
   <div class="form-group">
      {{ Form::label('superuser', "Superuser?") }}
      {{ Form::select('superuser', 
                      array('1' => 'Yes', '0' => 'No'), $user->superuser?'1':'0') }}
   </div>
   <br>
   <div class="form-group">
	  {{ Form::submit('Update User', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
