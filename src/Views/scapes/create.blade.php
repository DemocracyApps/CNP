@extends('layouts.default')

@section('content')
<h1>Create New Scape</h1>

{{ Form::open(['route' => 'scapes.store']) }}
   <div>
      {{ Form::label('name', 'Name: ') }}
      {{ Form::input('text', 'name') }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div>
      {{ Form::label('access', 'Access: ') }}
      {{Form::select('access', array('Open' => 'Open', 'Closed' => 'Closed', 'Private' => 'Private'))}}
   </div>
   <br/>
   <div>
      {{ Form::label('content', 'Notes: ') }}
      {{ Form::textarea('content') }}
      <br/>
   </div>
   <div>
	{{ Form::submit('Create') }}
   </div>
{{ Form::close() }}

@stop
