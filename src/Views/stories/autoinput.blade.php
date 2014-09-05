@extends('layouts.default')

@section('content')
<h1>Tell Your New Story</h1>

{{ Form::open(['route' => 'stories.store']) }}
   <input type="hidden" name="driver" value="{{$driver->id}}"/>
   <?php
      $done = false;
      $count = 0;
      while ( ! $done ) {
         $next = $driver->getNextInput();
         if ( ! $next)
            $done = true;
         else
            echo "<p>".$next['tag']."</p>";

         if ($count > 10) $done = true;
         ++$count;
         if ($next['pagebreak']) break;
      }
      $driver->save();
   ?>

   <div class="form-group">
	{{ Form::submit('Submit Story', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
