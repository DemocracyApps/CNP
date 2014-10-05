@extends('layouts.default')

@section('content')
<h1>{{ $composer->name }} </h1>

<!-- Edit Composer Button -->
{{ Form::open(array('route' => array('composers.edit', $composer->id), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-info btn-mini">Edit</button>
{{ Form::close() }}

<!-- Run Composer Button -->
{{ Form::open(array('route' => array('compositions.create'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="composer" value="{{$composer->id}}"/>
  <button type="submit" class="btn btn-info btn-mini">Use</button>
{{ Form::close() }}

<!-- Delete Composer Button -->
{{ Form::open(array('route' => array('composers.destroy', $composer->id), 'method' => 'delete',
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-danger btn-mini">Delete</button>
{{ Form::close() }}

{{ link_to("projects/".$composer->project, "Return to Project Page") }}
<br/>

<div>
  <h2>General Information</h2>
  <table class="table">
    <tr>
      <th>Specification Name:</th>  <td>{{$composer->name}}</td>
    </tr>
    <tr>
      <th>Project:</th>  <td>{{$composer->project}}</td>
    </tr>
    <tr>
      <th>Description:</th>  <td>{{$composer->description}}</td
    </tr>
    <tr>
      <th>Contains:</th> <td>{{$composer->contains}}</td>
    </tr>
    <tr>
      <th>Depends On:</th> <td>{{$composer->dependson}}</td>
    </tr>
    <tr>
      <th>Preferred Output Composer:</th> <td>{{$composer->output}}</td>
    </tr>
  </table>
</div>

<div>
  <h2>Composer Specification</h2>
  <br/>
  <pre>
    <code>
    {{$composer->specification}}
    </code>
  </pre>
</div>

@stop
