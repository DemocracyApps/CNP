@extends('templates.default')

@section('content')
<h1> Edit Project</h1>
<?php
        $access = $project->getProperty('access');
        $secret = "";
        if ($project->hasProperty('secret')) $secret = $project->getProperty('secret');
?>

<form method="POST" action="/admin/projects/{!! $project->id !!}" enctype="multipart/form-data">
   <input name="_method" type="hidden" value="PUT">
   <input type="hidden" name="_token" value="{!! csrf_token() !!}">


   <div class="form-group">
      {!!  Form::label('name', 'Name: ')  !!}
      {!!  Form::text('name', $project->name, ['class' => 'form-control'])  !!}
      <br/>
      <span class="error">{!!  $errors->first('name')  !!}</span>
   </div>
   <br/>
   <div class="form-group">
      {!!  Form::label('access', 'Access: ')  !!}

      <select id="access" name="access" class="form-control" onchange="toggleSecret();">
          <option value="Open" {!! ($access=='Open')?'selected':' ' !!}>Open</option>
          <option value="Closed" {!! ($access=='Closed')?'selected':' ' !!}>Closed</option>
          <option value="Private" {!! ($access=='Private')?'selected':' ' !!}>Private</option>
      </select>
   </div>
   <br>
   <div id="termsDiv" class="form-group" style="display:none;">
      {!!  Form::label('terms', 'Load New Terms & Conditions (optional)') !!}
      {!!  Form::file('terms') !!}
      <span class="error">{!!  $errors->first('fileerror')  !!}</span>
   </div>

   <br/>
   <div id="secretDiv" class="form-group" style="display:none;">

      {!!  Form::label('secret', 'Secret (optional): ')  !!}

      {!!  Form::text('secret', $secret, ['class'=>'form-control'])  !!}
   </div>
   <br/>

   <div class="form-group">
      {!!  Form::label('content', 'Description: ')  !!}
      {!!  Form::textarea('content', $project->description, ['class' => 'form-control'])  !!}
      <br/>
   </div>
   <div>
      <input class="btn btn-primary" type="submit" value="Save">   </div>

   </form>
@stop

@section('scripts')
<script type="text/javascript">
   $(document).ready(function () { // Make sure the visibility matches the value, even on reload.
      toggleSecret();
   });

   function toggleSecret() {
      var value = $("#access").val();
      var div1 = document.getElementById("secretDiv");
      var div2 = document.getElementById("termsDiv");
      if (value == "Open") {
         div1.style.display='none';
         div2.style.display='none';
      }
      else {
         div1.style.display = 'block';
         div2.style.display = 'block';
      }
   }
</script>

@stop
