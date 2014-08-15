@extends('layouts.default')

@section('content')
<h1>All the Stories</h1>

	<ul>
	@foreach($stories as $story)
		<li>{{ $story->getId() }} {{ $story->getName()}}</li>
	@endforeach
	</ul>
    <br/>
        {{ Form::open(['route' => 'stories.create', 'method' => 'get']) }}
           <div>
             {{ Form::submit('Add a Story') }}
           </div>
        {{ Form::close() }}
@stop
