@extends('layouts.list')

@section('title')
      {{ Auth::user()->name }}'s Projects 
@stop

@section('buttons')
    <div class="col-xs-6">
      <button style="float:right;" class="btn btn-success btn-sm" onclick="window.location.href='/projects/create'">New</button>
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
    @foreach ($projects as $project)
      <tr>
        <td> {{ $project->id}} </td>
        <th> {{ link_to("projects/".$project->id, $project->name) }} </th>
        <td> {{ $project->getProperty('access')}} </td>
        <td> {{ $project->description }} </td>
      </tr>    
    @endforeach
  </table>

@stop
