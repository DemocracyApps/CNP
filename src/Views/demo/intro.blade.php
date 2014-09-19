@extends('layouts.default')

@section('content')
<h1>Introduction</h1>

<ul>
  <li>Item number 1</li>
  <li>Item number 2</li>
  <li>Item number 3</li>
  <li>Item number 4</li>
</ul>

    <br/>
        {{ Form::open(['route' => 'stories.create', 'method' => 'get']) }}
           <div>
             {{ Form::submit('Add a Story') }}
           </div>
        {{ Form::close() }}
@stop
