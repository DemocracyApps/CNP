@extends('layouts.default')

@section('content')
<h1>Available RelationTypes</h1>

	<ul>
	@foreach($relationtypes as $relationtype)
		<li>{{ $relationtype->id }} {{ $relationtype->name}}, Inverse = {{ $relationtype->inverse }}</li>
	@endforeach
	</ul>
    <br/>
        {{ Form::open(['route' => 'relationtypes.create', 'method' => 'get']) }}
           <div>
             {{ Form::submit('Add a Relation Type') }}
           </div>
        {{ Form::close() }}
@stop
