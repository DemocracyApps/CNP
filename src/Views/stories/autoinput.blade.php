@extends('layouts.default')

@section('content')
<h1>Tell Your New Story</h1>

{{ Form::open(['route' => 'stories.store']) }}
   <input type="hidden" name="driver" value="{{$collector->getDriver()->id}}"/>
   <input type="hidden" name="collector" value="{{$collector->id}}"/>
   <?php
      $done = false;
      while ( ! $done ) {
         $next = $collector->getDriver()->getNextInput();
         if ( ! $next)
            $done = true;
         else
            if (array_key_exists('prompt', $next)) {
               \DemocracyApps\CNP\Utility\Html::createInput($next);
               echo("\n");
            }
         \DemocracyApps\CNP\Utility\Html::createSelfClosingElement('br');
         echo("\n");
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
@section('scripts')
    <script type="text/javascript">
//autocomplete
      $(function() {
         $(".auto-person").autocomplete({
            source: "http://cnp.dev/ajax/person?collector={{$collector->id}}&driver={{$collector->getDriver()->id}}",
            minLength: 1
         });
      });

      $( ".auto-person" ).autocomplete({
         select: function( event, ui ) {
            console.log(ui.item.value);
            event.preventDefault();
            console.log(ui.item);
            this.value = ui.item.label;
            $ ("#"+this.name+"_param").val(ui.item.value);
         }
});
    </script>
@stop

