@extends('layouts.default')

@section('content')
<h1>Vista: {{$vista->name}}</h1>
  <?php
    $getParams = null;
    if ($composer) $getParams = '?composer='.$composer.'&vista='.$vista->id;
  ?>

	<table class="long-table">
    @foreach($denizens as $denizen)
      <tr>
        <td style="width:20%;"> {{ $denizen->getId() }} </td> 
        <td style="width:80%;"> <a href="/denizens/{{$denizen->id}}{{$getParams}}">{{ $denizen->getName()}} </a></td>
      </tr>
    @endforeach
	</table>
@stop
