@extends('layouts.default')

@section('content')
<h1>Create New Collector</h1>

{{ Form::open(['route' => 'collectors.store', 'files' => true]) }}
   {{ Form::hidden('scape', $scape)}}
   <div>
      {{ Form::label('name', 'Name: ') }}
      {{ Form::input('text', 'name') }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div>
      {{ Form::label('description', 'Description: ') }}
      {{ Form::textarea('description') }}
      <br/>
   </div>
   <div>
      {{ Form::file('collector')}}
   </div>
   <div>
	{{ Form::submit('Create') }}
   </div>
{{ Form::close() }}

@stop
