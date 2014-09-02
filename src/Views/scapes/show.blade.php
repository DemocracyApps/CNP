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
      <th> ID </th>
  </table>
  <br/>
        {{ Form::open(['route' => 'collector.upload', 'files' => true]) }}
           <div>
            {{ Form::file('collector')}}
            {{ Form::submit('Upload a Collector') }}
           </div>
        {{ Form::close() }}

</div>

@stop
