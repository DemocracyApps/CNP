@extends('layouts.list')

@section('title')
  {{$project->name}} Stories
@stop

@section('listContent')

	<table class="table table-striped">
    @foreach($stories as $story)
      <tr>
        <td style="width:20%;"> {{ $story->id }} </td> 
        <td style="width:80%;"> <a href="/stories/{{$story->top}}">{{ $story->title}} </a></td>
      </tr>
    @endforeach
	</table>
  <br/>
    {{$stories->appends(\Request::except('page'))->links()}}

@stop
