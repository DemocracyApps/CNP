@extends('layouts.default')

@section('content')
<h1>Create New Story</h1>

{{ Form::open(['route' => 'stories.store']) }}
   <div class="form-group">
      {{ Form::label('name', 'Title: ') }}
      {{ Form::text('name', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('content', 'Story Content: ') }}
      {{ Form::textarea('content', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error"> {{ $errors->first('content') }} </span>
   </div>
   <div class="form-group">
	{{ Form::submit('Submit Story', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
