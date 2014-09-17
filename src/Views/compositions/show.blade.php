@extends('layouts.default')

@section('content')
<h1>Auto-Output for <em>{{ $topDenizen->getName() }} </em></h1>

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
        ComposerOutputDriver::createInputDrivenOutput($topDenizen, $driver, $next);
         echo("\n");
       }
       \DemocracyApps\CNP\Utility\Html::createSelfClosingElement('br');
       echo("\n");
    }
  }
  $driver->cleanupAndSave();
?>

{{ Form::open(['url' => 'denizens/'.$topDenizen->id, 'method' => 'get']) }}
   <input type="hidden" name="driver" value="{{$composer->getDriver()->id}}"/>
   <input type="hidden" name="composer" value="{{$composer->id}}"/>
   <input type="hidden" name="vista" value="{{$vista}}"/>

   <div class="form-group">
      @if ($composer->getDriver()->done())
         {{ Form::submit('Done', ['class' => 'btn btn-primary']) }}
      @else
         {{ Form::submit('Next', ['class' => 'btn btn-primary']) }}
      @endif
   </div>
{{ Form::close() }}

@stop
