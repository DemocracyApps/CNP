@extends('layouts.default')

@section('content')
<h1>{{ $collector->name }} </h1>

<!-- Edit Collector Button -->
{{ Form::open(array('route' => array('collectors.edit', $collector->id), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button type="submit" href="{{ URL::route('collectors.edit', $collector->id) }}" class="btn btn-info btn-mini">Edit</button>
{{ Form::close() }}

<!-- Run Collector Button -->
<?php  $insert = 'spec='.$collector->id ?>
{{ Form::open(array('route' => array('stories.create'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="spec" value="{{$collector->id}}"/>
  <button type="submit" href="{{ URL::route('stories.create') }}" class="btn btn-info btn-mini">Run</button>
{{ Form::close() }}

<!-- Delete Collector Button -->
{{ Form::open(array('route' => array('collectors.destroy', $collector->id), 'method' => 'delete',
                                            'style' => 'display:inline-block')) }}
  <button type="submit" href="{{ URL::route('collectors.destroy', $collector->id) }}" class="btn btn-danger btn-mini">Delete</button>
{{ Form::close() }}

{{ link_to("scapes/".$collector->scape, "Return to Scape Page") }}
<br/>

<div>
  <h2>Collector Information</h2>
  <table>
    <tr>
      <th>Name:</th>  <td>{{$collector->name}}</td>
    </tr>
    <tr>
      <th>Scape:</th>  <td>{{$collector->scape}}</td>
    </tr>
    <tr>
      <th>Description:</th>  <td>{{$collector->description}}</td
    </tr>
    <tr>
      <th>Contains:</th> <td>{{$collector->contains}}</td>
    </tr>
    <tr>
      <th>Depends On:</th> <td>{{$collector->dependson}}</td>
    </tr>
  </table>
</div>

<div>
  <h2>Collector Specification</h2>
  <br/>
  <pre>
    <code>
    {{$collector->specification}}
    </code>
  </pre>
</div>

@stop
