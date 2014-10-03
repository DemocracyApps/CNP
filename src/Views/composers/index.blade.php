@extends('layouts.list')

@section('title')
      Input & Output Templates 
@stop

@section('listContent')

  <table class="table">
    <tr>
      <td></td>
      <td> ID </td>
      <td> Name </td>
      <td> Defines </td>
      <td> Dependency</td>
      <td> Project </td>
    </tr>
    @foreach ($composers as $composer)
      <tr>
        <td> <a class="label label-info" href="/stories/create?composer={{$composer->id}}">Use</a></td>
        <td> {{ $composer->id }} </td>
        <th> {{ link_to("composers/".$composer->id, $composer->name) }} </th>
        <td> {{ $composer->contains }}</td>
        <td> {{ $composer->dependson }}</td>
        <td> {{ $composer->project }} </td>
      </tr>    
    @endforeach
  </table>

@stop