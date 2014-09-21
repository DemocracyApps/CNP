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
  <h2>Vistas</h2>
  <table class="table">
    <tr>
      <th> ID </th>
      <th> Name </th>
      <th> Description </th>
      <th> Input Composers </th>
      <th> Output Composers </th>
      <th> Selectors </th>
      <th> Link </th>
    </tr>
    @foreach ($vistas as $vista)
      <tr>
        <td> {{ $vista->id }} </td>
        <td> {{ $vista->name }} </td>
        <td> {{ $vista->description }} </td>
        <td> {{ $vista->input_composers }}</td>
        <td> {{ $vista->output_composer }}</td>
        <td> {{ $vista->selector }}</td>
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
  <h2>Composer Specifications</h2>
  <table class="table">
    <tr>
      <th> Name </th>
      <th> Description </th>
      <th> ID </th>
      <th> Contains </th>
      <th> Depends On</th>
      <th> Use Link </th>
    </tr>
    @foreach ($composers as $composer)
      <tr>
        <th> {{ link_to("composers/".$composer->id, $composer->name) }} </th>
        <td> {{ $composer->description }} </td>
        <td> {{ $composer->id }} </td>
        <td> {{ $composer->contains }}</td>
        <td> {{ $composer->dependson }}</td>
        <td> <a href="/stories/create?composer={{$composer->id}}">Use</a></td>
      </tr>    
    @endforeach
  </table>
  <br/>
  {{ Form::open(['route' => 'composers.create', 'method' => 'get']) }}
     <div>
       {{ Form::hidden('scape', $scape->getId())}}
       {{ Form::submit('Add a Composer', ['class' => 'btn btn-primary']) }}
     </div>
  {{ Form::close() }}
</div>

@stop
