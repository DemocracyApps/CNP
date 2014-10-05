@extends('layouts.default')

@section('content')
<h1> <em>{{ $topElement->getName() }} </em></h1>

<?php
  use \DemocracyApps\CNP\Compositions\Outputs\ComposerOutputDriver;
  $driver = $composer->getDriver();
  $done = false;
  while (! $done ) {
    $next = $driver->getNext();
    if (! $next) {
      \Log::info("We are done");
      $done = true;
    }
    else {
      if (ComposerOutputDriver::validForOutput($next)) {
        \Log::info("Valid for input");
        ComposerOutputDriver::createInputDrivenOutput($topElement, $driver, $next);
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

{{ Form::open(['url' => 'compositions/'.$composition->id, 'method' => 'get']) }}
   <input type="hidden" name="driver" value="{{$composer->getDriver()->id}}"/>
   <input type="hidden" name="composer" value="{{$composer->id}}"/>
   <input type="hidden" name="composition" value="{{$composition->id}}"/>
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
