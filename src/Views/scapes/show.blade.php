@extends('layouts.default')

@section('content')
<h1>{{ $scape->getName() }} </h1>

<!-- Edit Scape Button -->
{{ Form::open(array('route' => array('scapes.edit', $scape->id), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button type="submit" href="{{ URL::route('scapes.edit', $scape->id) }}" class="btn btn-info btn-mini">Edit Scape</button>
{{ Form::close() }}

<!-- Download Stories Button -->
{{ Form::open(array('route' => array('stories.export'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="scape" value="{{$scape->id}}"/>
  <button type="submit" class="btn btn-info btn-mini">Export Stories</button>
{{ Form::close() }}

<!-- View Stories Button -->
{{ Form::open(array('route' => array('stories.index'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="scape" value="{{$scape->id}}"/>
  <button type="submit" class="btn btn-info btn-mini">View Stories</button>
{{ Form::close() }}

<!-- Delete Scape Button -->
{{ Form::open(array('route' => array('scapes.destroy', $scape->id), 'method' => 'delete',
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-danger btn-mini">Delete Scape</button>
{{ Form::close() }}

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
  <h2>Vistas</h2>
  <table>
    <tr>
      <th> ID </th>
      <th> Name </th>
      <th> Collector </th>
      <th> Description </th>
      <th> Top Level Elements</th>
      <th> Link </th>
    </tr>
    @foreach ($vistas as $vista)
      <tr>
        <td> {{ $vista->id }} </td>
        <td> {{ $vista->name }} </td>
        <td> {{ $vista->collector }}</td>
        <td> {{ $vista->description }} </td>
        <td> {{ $vista->topelements }}</td>
        <td> <a href="/vistas?vista={{$vista->id}}">View</a></td>
      </tr>    
    @endforeach
  </table>
  <br/>
  {{ Form::open(['route' => 'vistas.create', 'method' => 'get']) }}
     <div>
       {{ Form::hidden('scape', $scape->getId())}}
       {{ Form::submit('Add a Vista', ['class' => 'btn btn-primary']) }}
     </div>
  {{ Form::close() }}
</div>
<br/>
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
