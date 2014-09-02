@extends('layouts.default')

@section('content')
<h1>{{ $collector->name }} </h1>

{{ link_to("collectors/".$collector->id."/edit", "Edit Collector") }} <br/>
{{ link_to("scapes/".$collector->scape, "Return to Scape Page") }}

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
      <th>Description:</th>  <td>{{$collector->description}}</td>
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
