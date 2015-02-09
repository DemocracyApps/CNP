@extends('templates.default')

@section('title')
My Profile
@stop

@section('content')
  <ul class="nav nav-tabs">
    <li role="presentation" class="active"><a href="/user/profile">My Profile</a></li>
    <li role="presentation"><a href="/user/contributions">My Contributions</a></li>
  </ul>
  <br>

  <div class="row">
  <div class="col-sm-6">
    <div class="row">
      <div class="col-sm-4">
        <p><b>User ID:</b></p>
      </div>
      <div class="col-sm-2">
        <p>{!! $user->id !!}</p>
      </div>
      <div class="col-sm-6">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <p><b>User Name</b></p>
      </div>
      <div class="col-sm-8">
        <p>{!! $user->name !!}</p>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <p><b>Email:</b></p>
      </div>
      <div class="col-sm-2">
        <p>{!! $user->email !!}</p>
      </div>
      <div class="col-sm-6">
      </div>
    </div>

    <div class="row">
      <div class="col-sm-4">
        <button class="btn btn-info btn-med" onclick="window.location.href='/user/{!! $user->id !!}/edit'">Edit</button>
      </div>
      <div class="col-sm-8">
      </div>
    </div>
    <br>
  </div>
  <div class="col-sm-6">
  </div>
</div>

<br>
<a href="/logout">Log Out</a>
</div>

@stop
