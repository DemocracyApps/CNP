@extends('layouts.list')

@section('title')
      {{ Auth::user()->name }}'s Projects 
@stop

@section('buttons')
    <div class="col-xs-6">
      <button style="float:right;" class="btn btn-success btn-sm" onclick="window.location.href='/scapes/create'">New</button>
    </div>
@stop

@section('listContent')

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

@stop
