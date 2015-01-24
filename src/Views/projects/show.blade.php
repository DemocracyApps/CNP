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
{{ Form::open(array('url' => array($project->id), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-info btn-sm"><b>View Stories</b></button>
{{ Form::close() }}

<!-- Edit Project Button -->
{{ Form::open(array('route' => array('admin.projects.edit', $project->id), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button style="display:inline-block;" type="submit" href="{{ URL::route('admin.projects.edit', $project->id) }}" class="btn btn-info btn-sm"><b>Edit</b></button>
{{ Form::close() }}

<!-- Delete Project Button -->
{{ Form::open(array('route' => array('admin.projects.destroy', $project->id), 'method' => 'delete',
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-danger btn-sm"><b>Delete</b></button>
{{ Form::close() }}
@stop

@section('upperLeft')
<?php
  if (! $project->hasProperty('access')) {
    $project->setProperty('access', 'Open');
    $project->save();
  }
?>

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
    <p><b>Default Input Composer:</b></p>
  </div>
  <div class="col-sm-8">
    <?php
      $defaultInputComposer = null;
      if ($project->hasProperty('defaultInputComposer')) {
        $defaultInputComposer = $project->getProperty('defaultInputComposer');
      }
    ?>
    <select class="form-control" id="default-input-composer-select">
      @if (! $defaultInputComposer) {
        <option value="-1"> --- </option>
      }
      @else {
        <option value="-1" selected> --- </option>
      }
      @endif

      @foreach ($composers as $composer)
        @if ($defaultInputComposer == $composer->id) {
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

<div class="row">
  <div class="col-sm-4">
    <p><b>Default Output Composer:</b></p>
  </div>
  <div class="col-sm-8">
    <?php
      $defaultOutputComposer = null;
      if ($project->hasProperty('defaultOutputComposer')) {
        $defaultOutputComposer = $project->getProperty('defaultOutputComposer');
      }
    ?>
    <select class="form-control" id="default-output-composer-select">
      @if (! $defaultOutputComposer) {
        <option value="-1"> --- </option>
      }
      @else {
        <option value="-1" selected> --- </option>
      }
      @endif

      @foreach ($composers as $composer)
        @if ($defaultOutputComposer == $composer->id) {
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
  <!-- Composers -->
<hr/>
<br>
  <div class="row">
    <div class="col-xs-6">
      <h3>Input & Output Composers</h3>
    </div>
    <div class="col-xs-6">
      <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/admin/composers/create?project={{$project->id}}'">New</button>
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
        <td> <a class="label label-info" href="/{{$project->id}}/compositions/create?composer={{$defaultInputComposer}}">Use</a></td>
        {{--<td> <a class="label label-info" href="/compositions/create?composer={{$composer->id}}">Use</a></td>--}}
        <td> {{ $composer->id }} </td>
        <th> {{ link_to("admin/composers/".$composer->id, $composer->name) }} </th>
        <td> {{ $composer->contains }}</td>
        <td> {{ $composer->dependson }}</td>
      </tr>    
    @endforeach
  </table>

  <!-- Analyses -->
  <hr/>
  <br>
  <div class="row">
    <div class="col-xs-6">
      <h3>Analysis Tasks</h3>
    </div>
    <div class="col-xs-6">
      <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/admin/analysis/create?project={{$project->id}}'">New</button>
    </div>
  </div>

  <table class="table">
    <tr>
      <td> ID </td>
      <td> Name </td>
      <td> Notes</td>
    </tr>
    @foreach ($analyses as $analysis)
      <tr>
        <td> {{ $analysis->id }} </td>
        <th> {{ link_to("admin/analysis/".$analysis->id, $analysis->name) }} </th>
        <td> {{ $analysis->notes }}</td>
      </tr>
    @endforeach
  </table>


  @if ($project->getProperty('access') != 'Open')
    @if ($project->hasProperty('secret') && $project->getProperty('secret') != null)
      <br>
      <h3>Project Secret</h3>
      <p>{{ $project->getProperty('secret') }}</p>
    @endif
    @if ($project->terms != null)
      <br>
      <div>
        <h3>Project Terms & Conditions</h3>
        <br/>
        <?php
              $pd = new Parsedown();
              echo $pd->text($project->terms);
        ?>
        <!--
        <pre>
          <code>
            {{$project->terms}}
          </code>
        </pre>
        -->
      </div>
    @endif
  @endif


@stop

@section('footer_right')
  <a href="/{{$project->id}}">External Page</a>
@stop
@section('listScripts')
  <script type="text/javascript">
    function setDefaultInputComposer(event, ui)
    {
      var source ="http://cnp.dev/ajax/setProjectDefaultInputComposer?project={{$project->id}}&defaultInputComposer="
                  + $("select#default-input-composer-select").val();
      $.get( source, function( r ) {
        //alert("Got r = " + r.message);
      }).fail(function(r) {
        alert("Error saving default input composer: "+r.responseJSON.error.message);
      });
    }

    function setDefaultOutputComposer(event, ui)
    {
      var source ="http://cnp.dev/ajax/setProjectDefaultOutputComposer?project={{$project->id}}&defaultOutputComposer="
                  + $("select#default-output-composer-select").val();
      $.get( source, function( r ) {
      }).fail(function(r) {
        alert("Error saving default output composer: "+r.responseJSON.error.message);
      });
    }

    $("select#default-input-composer-select").change (setDefaultInputComposer);

    $("select#default-output-composer-select").change (setDefaultOutputComposer);


  </script>
  
@stop

