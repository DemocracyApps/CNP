@extends('layouts.default')

@section('content')
<h1>Create New Relation</h1>

{{ Form::open(['route' => 'stories.store']) }}
   <div>
      {{ Form::label('name', 'Title: ') }}
      {{ Form::input('text', 'name') }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div>
      {{ Form::label('content', 'Story Content: ') }}
      {{ Form::textarea('content') }}
      <br/>
      <span class="error"> {{ $errors->first('content') }} </span>
   </div>
   <div>
	{{ Form::submit('Submit Story') }}
   </div>
{{ Form::close() }}

@stop
