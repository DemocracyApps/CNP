@extends('layouts.detail')

@section('title')
{{ $project->name }}
@stop

@section('buttons')

<!-- Download Stories Button -->
{{ Form::open(array('route' => array('compositions.export'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="project" value="{{$project->id}}"/>
  <button type="submit" class="btn btn-info btn-sm"><b>Export Stories</b></button>
{{ Form::close() }}

<!-- View Stories Button -->
{{ Form::open(array('route' => array('compositions.index'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="project" value="{{$project->id}}"/>
  <button type="submit" class="btn btn-info btn-sm"><b>View Stories</b></button>
{{ Form::close() }}

<!-- Edit Project Button -->
{{ Form::open(array('route' => array('projects.edit', $project->id), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button style="display:inline-block;" type="submit" href="{{ URL::route('projects.edit', $project->id) }}" class="btn btn-info btn-sm"><b>Edit</b></button>
{{ Form::close() }}

<!-- Delete Project Button -->
{{ Form::open(array('route' => array('projects.destroy', $project->id), 'method' => 'delete',
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
    <p>{{$project->id}}</p>
  </div>
  <div class="col-sm-6">
  </div>
</div>
<div class="row">
  <div class="col-sm-4">
    <p><b>Access:</b></p>
  </div>
  <div class="col-sm-8">
    <p>{{$project->getProperty('access')}}</p>
  </div>
</div>
<div class="row">
  <div class="col-sm-4">
    <p><b>Default Composer:</b></p>
  </div>
  <div class="col-sm-8">
    <?php
      $defaultComposer = null;
      if ($project->hasProperty('defaultComposer')) {
        $defaultComposer = $project->getProperty('defaultComposer');
      }
    ?>
    <select id="default-composer-select">
      @if (! $defaultComposer) {
        <option value="-1"> --- </option>
      }
      @else {
        <option value="-1" selected> --- </option>
      }
      @endif

      @foreach ($composers as $composer)
        @if ($defaultComposer == $composer->id) {
          <option value="{{$composer->id}}" selected>{{$composer->name}}</option>
        }
        @else {
          <option value="{{$composer->id}}">{{$composer->name}}</option>
        }
        @endif
      @endforeach
    </select>
  </div>
</div>
@stop

@section('upperRight')
<div class="row">
  <p><b>Description:</b></p>
</div>
<div class="row">
  <p>{{$project->description}}</p>
</div>

@stop


@section('detailContent')
<hr/>
<br>
  <div class="row">
    <div class="col-xs-6">
      <h3>Input & Output Templates</h3>
    </div>
    <div class="col-xs-6">
      <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/composers/create?project={{$project->id}}'">New</button>
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
        <td> <a class="label label-info" href="/compositions/create?composer={{$composer->id}}">Use</a></td>
        <td> {{ $composer->id }} </td>
        <th> {{ link_to("composers/".$composer->id, $composer->name) }} </th>
        <td> {{ $composer->contains }}</td>
        <td> {{ $composer->dependson }}</td>
      </tr>    
    @endforeach
  </table>

@stop
@section('listScripts')
  <script type="text/javascript">
    function setDefaultComposer(event, ui)
    {
      var source ="http://cnp.dev/ajax/setProjectDefaultComposer?project={{$project->id}}&defaultComposer="
                  + $("select#default-composer-select").val();
      $.get( source, function( r ) {
        //alert("Got r = " + r.message);
      }).fail(function(r) {
        alert("Error saving default composer: "+r.responseJSON.error.message);
      });
    }
    $("select#default-composer-select").change (setDefaultComposer);


  </script>
  
@stop

