@extends('layouts.default')

@section('content')
<h1>Create New Project</h1>

{{ Form::open(['route' => 'admin.projects.store']) }}
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
   <br/>
   <div id="secretDiv" class="form-group" style="display:none;">

      {{ Form::label('secret', 'Secret (optional): ') }}

      {{ Form::text('secret', null, ['class'=>'form-control']) }}
   </div>
   <br/>

   <div class="form-group">
      {{ Form::label('content', 'Notes: ') }}
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
   function toggleSecret() {

      var value = $("#access").val();
      var div = document.getElementById("secretDiv");
      if (value == "Open") {
         div.style.display='none';
      }
      else {
         div.style.display = 'block';
      }
   }
</script>

@stop
