@extends('templates.default')

@section('content')
<h1>Upload CSV File of Stories</h1>

<p>We will use Composer specification {!!  $composer->id  !!} ({!! $composer->name !!}) to process your stories.</p>
<br/>
<form method="POST" action="http://cnp.dev/{!! $composer->project !!}/compositions"
      accept-charset="UTF-8" enctype="multipart/form-data">
   <input type="hidden" name="_token" value="{!! csrf_token() !!}">
   <input type="hidden" name="composition" value="{!! $composition->id !!}">

   <div class="form-group">
      {!!  Form::label('csv', 'CSV File') !!}
      {!!  Form::file('csv') !!}
      
      <span class="error">{!!  $errors->first('csv')  !!}</span>
   </div>
   <br/>
   <div class="form-group">
	  {!!  Form::submit('Upload File', ['class' => 'btn btn-primary'])  !!}
   </div>

</form>
@stop
