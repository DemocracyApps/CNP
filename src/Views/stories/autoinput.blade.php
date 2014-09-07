@extends('layouts.default')

@section('content')
<h1>Tell Your New Story</h1>

{{ Form::open(['route' => 'stories.store']) }}
   <input type="hidden" name="driver" value="{{$collector->getDriver()->id}}"/>
   <input type="hidden" name="collector" value="{{$collector->id}}"/>
   <?php
      $done = false;
      $count = 0;
      while ( ! $done ) {
         $next = $collector->getDriver()->getNextInput();
         if ( ! $next)
            $done = true;
         else
            if (array_key_exists('prompt', $next)) {
               \DemocracyApps\CNP\Utility\Html::createInput($next);
            }
         \DemocracyApps\CNP\Utility\Html::createSelfClosingElement('br');

         if ($count > 10) $done = true;
         ++$count;
      }
      $collector->getDriver()->cleanupAndSave();
   ?>

   <div class="form-group">
      @if ($collector->getDriver()->inputDone())
         {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
      @else
         {{ Form::submit('Next', ['class' => 'btn btn-primary']) }}
      @endif
   </div>
{{ Form::close() }}

@stop
