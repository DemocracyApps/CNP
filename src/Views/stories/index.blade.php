@extends('layouts.default')

@section('content')
<h1>We Have the Stories</h1>

	<table class="long-table">
    @foreach($stories as $story)
      <tr>
        <td style="width:20%;"> {{ $story->getId() }} </td> 
        <td style="width:80%;"> <a href="/stories/{{$story->id}}">{{ $story->getName()}} </a></td>
      </tr>
    @endforeach
	</table>
    <br/>
        {{ Form::open(['route' => 'stories.create', 'method' => 'get']) }}
           <div>
             {{ Form::submit('Add a Story') }}
           </div>
        {{ Form::close() }}
@stop
