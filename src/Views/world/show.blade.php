@extends('layouts.default_ext')

@section('buttons')
    <button class="btn btn-warning" style="width:100px;" onclick="window.location.href='/{{$project}}'">Home</button>
@stop

@section('content')
<h1> <em>{{ $composition->title }} </em></h1>

<?php
  use \DemocracyApps\CNP\Compositions\Outputs\ComposerOutputDriver;
  $driver = $composer->getDriver();
  $done = false;
  while (! $done ) {
    $next = $driver->getNext();
    if (! $next) {
      $done = true;
    }
    else {
      if (ComposerOutputDriver::validForOutput($next)) {
        ComposerOutputDriver::createInputDrivenOutput($composition->id, $topElement, $driver, $next);
         echo("\n");
       }
       else {
        \Log::info("Not valid for input");
       }
       \DemocracyApps\CNP\Utility\Html::createSelfClosingElement('br');
       echo("\n");
    }
  }
  $driver->cleanupAndSave();
?>

{{ Form::open(['url' => '/'.$composer->project.'/compositions/'.$composition->id, 'method' => 'get']) }}
   <input type="hidden" name="driver" value="{{$composer->getDriver()->id}}"/>
   <input type="hidden" name="composer" value="{{$composer->id}}"/>
   <input type="hidden" name="composition" value="{{$composition->id}}"/>

   <div class="form-group">
      @if ($composer->getDriver()->done())
         {{ Form::submit('Done', ['class' => 'btn btn-primary']) }}
      @else
         {{ Form::submit('Next', ['class' => 'btn btn-primary']) }}
      @endif
   </div>
{{ Form::close() }}

@stop
