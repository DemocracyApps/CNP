@extends('layouts.default')

@section('content')
<h1>{{ $scape->getName() }} </h1>

<div>
  <h2>Scape Information</h2>
  <table>
    <tr>
      <th>Name:</th>  <td>{{$scape->getName()}}</td>
    </tr>
    <tr>
      <th>Access:</th>  <td>{{$scape->getProperty('access')}}</td>
    </tr>
    <tr>
      <th>Content:</th>  <td>{{$scape->getContent()}}</td>
    </tr>
  </table>
</div>

<div>
  <h2>Collectors</h2>
  <table>
    <tr>
      <th> Name </th>
      <th> Description </th>
      <th> ID </th>
    </tr>
    @foreach ($collectors as $collector)
      <tr>
        <th> {{ link_to("collectors/".$collector->id, $collector->name) }} </th>
        <td> {{ $collector->description }} </td>
        <td> {{ $collector->id }} </td>
      </tr>    
    @endforeach
  </table>
  <br/>
  {{ Form::open(['route' => 'collectors.create', 'method' => 'get']) }}
     <div>
       {{ Form::hidden('scape', $scape->getId())}}
       {{ Form::submit('Add a Collector', ['class' => 'btn btn-primary']) }}
     </div>
  {{ Form::close() }}
</div>

@stop
