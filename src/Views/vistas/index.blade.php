@extends('layouts.list')

@section('title')
      Project Views 
@stop

@section('listContent')

  <table class="table">
    <tr>
      <td>  </td>
      <td> ID </td>
      <td> Name </td>
      <td> Description </td>
      <td> Allowed Inputs </td>
      <td> Allowed Outputs </td>
      <td> Selectors </td>
      <td> Project </td>
    </tr>
    @foreach ($vistas as $vista)
      <tr>
        <td> <a class="label label-info" href="/stories?vista={{$vista->id}}">View</a></td>
        <td> {{ $vista->id }} </td>
        <td> {{ $vista->name }} </td>
        <td> {{ $vista->description }} </td>
        <td> {{ $vista->input_composers }}</td>
        <td> {{ $vista->output_composer }}</td>
        <td> {{ $vista->selector }}</td>
        <td> {{ $vista->project }}</td>
      </tr>    
    @endforeach
  </table>

@stop