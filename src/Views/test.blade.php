@extends('layouts.default')

@section('content')

<h1> Create a Post</h1>

{{ Form::open() }}
	<div class="form-group">
		{{ Form::label('title', 'title:') }}
		{{ Form::text('title',null,['class' => 'form-control']) }}
	</div>

	<div>
		{{ Form::submit('Create Post', ['class' => 'btn btn-default']) }}
	</div>
@stop
