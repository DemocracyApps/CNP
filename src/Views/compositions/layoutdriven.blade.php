@extends('layouts.default')

@section('content')
<h1>Presentation Using View: {{ $vista->name }}</h1>

<?php
  use \DemocracyApps\CNP\Compositions\Outputs\ComposerOutputDriver;
  $driver = $composer->getDriver();
  $driver->getOutputContent($topDenizen, $vista->id, 2);
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
