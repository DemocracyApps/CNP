@extends('layouts.default')

@section('content')
<h1>Tell Your New Story</h1>

<?php

   function createElement($type, $content, $properties)
   {
      $nonSelfClosingElements = array('textarea'=>true);
      echo "<".$type;
      foreach ($properties as $key => $value) {
         echo " ". $key . "=\"".$value."\"";
      }
      if ($content) {
         echo ">";
         echo $content;
         echo "</".$type.">";
      }
      else {
         if (array_key_exists($type, $nonSelfClosingElements)) {
            echo ">";
            echo "</".$type.">";
         }
         else {
            echo "/>";
         }
      }
   }
   function startElement($type, $properties)
   {
      echo "<".$type;
      foreach ($properties as $key => $value) {
         echo " ". $key . "=\"".$value."\"";
      }
      echo ">";
   }
   function endElement($type)
   {
      echo "</".$type.">";
   }

   function createInput($desc)
   {
      startElement("div", array('class' => 'form-group'));
      createElement("label", $desc['prompt'], array('for' => $desc['tag']));
      if ($desc['type'] == 'text') {
         createElement('input', null, array('class' => 'form-control', 'name' => $desc['tag'], 'type'=>'text'));
      }
      elseif ($desc['type'] == 'textarea') {
         createElement('textarea', null, array('class' => 'form-control', 'name' => $desc['tag'],
                       'cols'=>'50', 'rows' => '10'));
      }
      endElement("div");

   }
?>
{{ Form::open(['route' => 'stories.store']) }}
   <input type="hidden" name="driver" value="{{$driver->id}}"/>
   <input type="hidden" name="collector" value="{{$collector->id}}"/>
   <?php
      $done = false;
      $count = 0;
      while ( ! $done ) {
         $next = $driver->getNextInput();
         if ( ! $next)
            $done = true;
         else
            if (array_key_exists('prompt', $next)) {
               createInput($next);
            }

         if ($count > 10) $done = true;
         ++$count;
      }
      $driver->cleanupAndSave();
   ?>

   <div class="form-group">
      @if ($driver->inputDone())
         {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
      @else
         {{ Form::submit('Next', ['class' => 'btn btn-primary']) }}
      @endif
   </div>
{{ Form::close() }}

@stop
