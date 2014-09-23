@extends('layouts.list')

@section('title')
  {{$vista->name}}
@stop

@section('listContent')
  <?php
    $getParams = null;
    if ($composer) $getParams = '?composer='.$composer.'&vista='.$vista->id;
  ?>

	<table class="table table-striped">
    @foreach($denizens as $denizen)
      <tr>
        <td style="width:20%;"> {{ $denizen->getId() }} </td> 
        <td style="width:80%;"> <a href="/denizens/{{$denizen->id}}{{$getParams}}">{{ $denizen->getName()}} </a></td>
      </tr>
    @endforeach
	</table>
  {{$denizens->appends(\Request::except('page'))->links()}}
@stop
