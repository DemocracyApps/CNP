@extends('templates.default')

@section('content')
   <h1>Edit User</h1>

   <form method="POST" action="{!! url('user') . '/' . $user->id !!}">
      <input name="_method" type="hidden" value="PUT">
      <input type="hidden" name="_token" value="{!! csrf_token() !!}">

      <div class="form-group">
         {!!  Form::label('name', 'Name: ')  !!}
         {!!  Form::text('name', $user->name, ['class' => 'form-control'])  !!}
         <br>
         <span class="error">{!!  $errors->first('name')  !!}</span>
      </div>
      <br>
      <div class="form-group">
         {!!  Form::label('email', 'Email: ')  !!}
         {!!  Form::text('email', $user->email, ['class' => 'form-control'])  !!}
         <br>
         <span class="error">{!!  $errors->first('email')  !!}</span>
      </div>
      <br>
      @if ($system)
         <div class="form-group">
            {!!  Form::label('projectcreator', "Project Creator?")  !!}
            {!!  Form::select('projectcreator',
                            array('1' => 'Yes', '0' => 'No'), $user->projectcreator?'1':'0')  !!}
         </div>
         <br>
         <div class="form-group">
            {!!  Form::label('superuser', "Superuser?")  !!}
            {!!  Form::select('superuser',
                            array('1' => 'Yes', '0' => 'No'), $user->superuser?'1':'0')  !!}
         </div>
         <br>
      @endif
      <div class="form-group">
         <input type="submit" style="width:200px;" class='btn btn-primary' value="Update Profile">
      </div>
   </form>

@stop
