@extends('layouts.detail')

@section('title')
{{ $scape->getName() }}
@stop

@section('buttons')

<!-- Download Stories Button -->
{{ Form::open(array('route' => array('stories.export'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="scape" value="{{$scape->id}}"/>
  <button type="submit" class="btn btn-info btn-sm"><b>Export Stories</b></button>
{{ Form::close() }}

<!-- View Stories Button -->
{{ Form::open(array('route' => array('stories.index'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="scape" value="{{$scape->id}}"/>
  <button type="submit" class="btn btn-info btn-sm"><b>View Stories</b></button>
{{ Form::close() }}

<!-- Edit Scape Button -->
{{ Form::open(array('route' => array('scapes.edit', $scape->id), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button style="display:inline-block;" type="submit" href="{{ URL::route('scapes.edit', $scape->id) }}" class="btn btn-info btn-sm"><b>Edit</b></button>
{{ Form::close() }}

<!-- Delete Scape Button -->
{{ Form::open(array('route' => array('scapes.destroy', $scape->id), 'method' => 'delete',
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-danger btn-sm"><b>Delete</b></button>
{{ Form::close() }}
@stop

@section('upperLeft')
<div class="row">
  <div class="col-sm-4">
    <p><b>Project ID:</b></p>
  </div>
  <div class="col-sm-2">
    <p>{{$scape->id}}</p>
  </div>
  <div class="col-sm-6">
  </div>
</div>
<div class="row">
  <div class="col-sm-4">
    <p><b>Access:</b></p>
  </div>
  <div class="col-sm-2">
    <p>{{$scape->getProperty('access')}}</p>
  </div>
  <div class="col-sm-6">
  </div>
</div>
@stop

@section('upperRight')
<div class="row">
  <p><b>Description:</b></p>
</div>
<div class="row">
  <p>{{$scape->getContent()}}</p>
</div>

@stop


@section('detailContent')
<hr/>
<br>
  <div class="row">
    <div class="col-xs-6">
      <h3>Input & Output Specifications</h3>
    </div>
    <div class="col-xs-6">
      <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/composers/create?scape={{$scape->id}}'">New</button>
    </div>
  </div>

  <table class="table">
    <tr>
      <td></td>
      <td> ID </td>
      <td> Name </td>
      <td> Defines </td>
      <td> Dependency</td>
    </tr>
    @foreach ($composers as $composer)
      <tr>
        <td> <a class="label label-info" href="/stories/create?composer={{$composer->id}}">Use</a></td>
        <td> {{ $composer->id }} </td>
        <th> {{ link_to("composers/".$composer->id, $composer->name) }} </th>
        <td> {{ $composer->contains }}</td>
        <td> {{ $composer->dependson }}</td>
      </tr>    
    @endforeach
  </table>
  <br/>

<div>
  <div class="row">
    <div class="col-xs-6">
      <h3>Project Views</h3>
    </div>
    <div class="col-xs-6">
      <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/vistas/create?scape={{$scape->id}}'">New</button>
    </div>
  </div>
  <table class="table">
    <tr>
      <td>  </td>
      <td> ID </td>
      <td> Name </td>
      <td> Description </td>
      <td> Allowed Inputs </td>
      <td> Allowed Outputs </td>
      <td> Selectors </td>
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
      </tr>    
    @endforeach
  </table>
</div>

@stop
