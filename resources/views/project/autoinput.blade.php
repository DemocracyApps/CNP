@extends('templates.default')

@section('content')
<h1>Tell Your Story</h1>

<form method="POST" action="http://cnp.dev/{!! $composer->project !!}/compositions"
      accept-charset="UTF-8" enctype="multipart/form-data">
   <input type="hidden" name="_token" value="{!! csrf_token() !!}">

   <input type="hidden" name="driver" value="{!! $composer->getDriver()->id !!}"/>
   <input type="hidden" name="composition" value="{!! $composition->id !!}"/>
   @if ($composer->getReferentId())
      <input type="hidden" name="referentId" value="{!! $composer->getReferentId() !!}"/>
   @endif
   @if($composer->getReferentRelation())
      <input type="hidden" name="referentRelation" value="{!! $composer->getReferentRelation() !!}"/>
   @endif
   <?php
      use \DemocracyApps\CNP\Project\Compositions\Inputs\ComposerInputDriver;
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
         {!!  Form::submit('Submit', ['class' => 'btn btn-primary'])  !!}
      @else
         {!!  Form::submit('Next', ['class' => 'btn btn-primary'])  !!}
      @endif
   </div>
</form>

@stop
@section('scripts')
   <?php
   JavaScript::put([
           'ajaxPath' => Util::ajaxPath('project', 'autoinput'),
           'composer' => $composer->id,
           'driver' => $composer->getDriver()->id
   ]);
   ?>

   <script type="text/javascript">
       function sliderChange (target) {
          var value = $("#"+target+"-control").val();
          $("#"+target+"-display").html(value);
       }

    </script>
@stop

