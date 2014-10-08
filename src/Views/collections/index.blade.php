@extends('layouts.list')

@section('title')
      {{ Auth::user()->name }}'s Projects 
@stop

@section('buttons')
    <div class="col-xs-6">
      <button style="float:right;" class="btn btn-success btn-sm" onclick="window.location.href='/collections/create'">New Collection</button>
    </div>
@stop

@section('listContent')

  <table class="table">
    <tr>
      <th> ID </th>
      <th> Name </th>
      <th>  </th>
    </tr>
    @foreach ($collections as $collection)
      <tr>
        <td> {{ $collection->id}} </td>
        <th> {{ link_to("collections/".$collection->id, $collection->name) }} </th>
        <th> {{ link_to("collections/edit/".$collection->id, "Edit") }} </th>
      </tr>    
    @endforeach
  </table>

@stop
