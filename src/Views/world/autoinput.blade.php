@extends('layouts.default_ext')

@section('content')
<h1>Tell Your New Story</h1>

<form method="POST" action="http://cnp.dev/{{$composer->project}}/compositions" accept-charset="UTF-8">
   {{Form::token();}}
   <input type="hidden" name="driver" value="{{$composer->getDriver()->id}}"/>
   <input type="hidden" name="composition" value="{{$composition->id}}"/>
   @if ($composer->getReferentId())
      <input type="hidden" name="referentId" value="{{$composer->getReferentId()}}"/>
      <input type="hidden" name="referentRelation" value="{{$composer->getReferentRelation()}}"/>
   @endif
   <?php
      use \DemocracyApps\CNP\Compositions\Inputs\ComposerInputDriver;
      $done = false;
      while ( ! $done ) {
         $next = $composer->getDriver()->getNext();
         if ( ! $next) {
            $done = true;
         }
         else {
            if (ComposerInputDriver::validForInput($next)) {
               ComposerInputDriver::createInput($next);
               echo("\n");
            }
         }
         \DemocracyApps\CNP\Utility\Html::createSelfClosingElement('br');
         echo("\n");
      }
      $composer->getDriver()->cleanupAndSave();
   ?>

   <div class="form-group">
      @if ($composer->getDriver()->done())
         {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
      @else
         {{ Form::submit('Next', ['class' => 'btn btn-primary']) }}
      @endif
   </div>
</form>

@stop
@section('scripts')
    <script type="text/javascript">
//autocomplete
      $(function() {
         $(".auto-person").autocomplete({
            source: "http://cnp.dev/ajax/person?composer={{$composer->id}}&driver={{$composer->getDriver()->id}}",
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

