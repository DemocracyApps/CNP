@extends('layouts.default_ext')

@section('buttons')
    <button class="btn btn-warning" style="width:100px;" onclick="window.location.href='/{{$project}}'">Home</button>
@stop

@section('content')
<h1>{{ $composition->title }}</h1>
<div class="row">
  <div class="col-md-8"></div>
  <div class="col-md-4">
    <p><a href="/{{$composer->project}}/compositions/{{$composition->id}}?view=structure">Structure View</a></p>
  </div>
</div>

<?php
  use \DemocracyApps\CNP\Compositions\Outputs\ComposerOutputDriver;
  $driver = $composer->getDriver();
  $driver->getOutputContent($topElement, $composition);
?>

{{ Form::open(['url' => '/'.$composer->project.'/compositions/'.$composition->id, 'method' => 'get']) }}
   <input type="hidden" name="driver" value="{{$composer->getDriver()->id}}"/>
   <input type="hidden" name="composer" value="{{$composer->id}}"/>

   <div class="form-group">
      @if ($composer->getDriver()->done())
         {{ Form::submit('Done', ['class' => 'btn btn-primary']) }}
      @else
         {{ Form::submit('Next', ['class' => 'btn btn-primary']) }}
      @endif
   </div>
{{ Form::close() }}

@stop
