@extends('layouts.list')

@section('title')
      {{ Auth::user()->name }}'s Projects 
@stop

@section('buttons')
    <div class="col-xs-6">
      <button style="float:right;" class="btn btn-success btn-sm" onclick="window.location.href='/admin/projects/create'">New Project</button>
    </div>
@stop

@section('listContent')

  <table class="table">
    <tr>
      <th> ID </th>
      <th> Name </th>
      <th> Access </th>
      <th>  </th>
      <th>  </th>
    </tr>
    @foreach ($projects as $project)
      <tr>
        <td> {{ $project->id}} </td>
        <th> {{ link_to("admin/projects/".$project->id, $project->name) }} </th>
        <td> {{ $project->getProperty('access')}} </td>
        <td> <a class="label label-info" style="position:relative; top:5px;"
                href="/compositions?project={{$project->id}}">View Stories</a></td>
        <td> <a class="label label-info" style="position:relative; top:5px;"
                href="/compositions/explore?project={{$project->id}}">Curate</a></td>
      </tr>    
    @endforeach
  </table>

@stop
