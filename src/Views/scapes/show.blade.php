@extends('layouts.default')

@section('content')
<h1>{{ $scape->getName() }} </h1>

{{ link_to("/account", "Return to Account Page") }}

<div>
  <h2>General Information</h2>
  <table>
    <tr>
      <th>Scape Name:</th>  <td>{{$scape->getName()}}</td>
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
  <h2>Collector Specifications</h2>
  <table>
    <tr>
      <th> Name </th>
      <th> Description </th>
      <th> ID </th>
      <th> Contains </th>
      <th> Depends On</th>
      <th> Use Link </th>
    </tr>
    @foreach ($collectors as $collector)
      <tr>
        <th> {{ link_to("collectors/".$collector->id, $collector->name) }} </th>
        <td> {{ $collector->description }} </td>
        <td> {{ $collector->id }} </td>
        <td> {{ $collector->contains }}</td>
        <td> {{ $collector->dependson }}</td>
        <td> <a href="/stories/create?collector={{$collector->id}}">Use</a></td>
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
