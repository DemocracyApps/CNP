@extends('layouts.list')

@section('title')
  All Stories - Default Views
@stop

@section('buttons')
    <div class="col-xs-6">
      <button style="float:right;" class="btn btn-success btn-sm" onclick="window.location.href='/vistas'">Project Views</button>
    </div>
@stop

@section('listContent')

	<table class="table table-striped">
    @foreach($stories as $story)
      <tr>
        <td style="width:20%;"> {{ $story->getId() }} </td> 
        <td style="width:80%;"> <a href="/stories/{{$story->id}}">{{ $story->getName()}} </a></td>
      </tr>
    @endforeach
	</table>
  <br/>
    {{$stories->appends(\Request::except('page'))->links()}}

@stop
