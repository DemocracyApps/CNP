@extends('layouts.default')

@section('content')
<h1>Create New Project</h1>

{{ Form::open(['route' => 'admin.projects.store', 'files' => true]) }}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('access', 'Access: ') }}

      <select id="access" name="access" class="form-control" onchange="toggleSecret();">
          <option value="Open">Open</option>
          <option value="Closed">Closed</option>
          <option value="Private">Private</option>
      </select>
   </div>
   <br>
   <div id="termsDiv" class="form-group" style="display:none;">
      {{ Form::label('terms', 'Terms & Conditions (optional)')}}
      {{ Form::file('terms')}}
      <span class="error">{{ $errors->first('fileerror') }}</span>
   </div>

   <br/>
   <div id="secretDiv" class="form-group" style="display:none;">

      {{ Form::label('secret', 'Secret (optional): ') }}

      {{ Form::text('secret', null, ['class'=>'form-control']) }}
   </div>
   <br/>

   <div class="form-group">
      {{ Form::label('content', 'Description: ') }}
      {{ Form::textarea('content', null, ['class' => 'form-control']) }}
      <br/>
   </div>
   <div>
	{{ Form::submit('Create', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

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
