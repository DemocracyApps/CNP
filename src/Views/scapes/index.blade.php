@extends('layouts.default')

@section('content')
<div class="row">
  <div class="col-md-6">
    <h1>{{ Auth::user()->name }}'s Projects </h1>
    <br>
  </div>
  <div class="col-md-3">
  </div>
</div>
<div class="row">
  <div class="col-md-9">
  </div>
  <div class="col-md-3" style="margin-bottom:10px;">
    <div style="float:right;">
      {{ Form::open(['route' => 'scapes.create', 'method' => 'get']) }}
         <div>
           {{ Form::submit('New Project',['class' => 'btn btn-info']) }}
         </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
<div class="row">
  <table class="table">
    <tr>
      <th> ID </th>
      <th> Name </th>
      <th> Access </th>
      <th> Description </th>
    </tr>
    @foreach ($scapes as $scape)
      <tr>
        <td> {{ $scape->id}} </td>
        <th> {{ link_to("scapes/".$scape->id, $scape->name) }} </th>
        <td> {{ $scape->getProperty('access')}} </td>
        <td> {{ $scape->content }} </td>
      </tr>    
    @endforeach
  </table>
</div>

@stop
