@extends('layouts.detail_ext')

@section('title')
Account Page
@stop

@section('upperLeft')

<div class="row">
  <div class="col-sm-4">
    <p><b>User Name</b></p>
  </div>
  <div class="col-sm-8">
    <p>{{$user->name}}</p>
  </div>
</div>
<div class="row">
  <div class="col-sm-4">
    <p><b>User ID:</b></p>
  </div>
  <div class="col-sm-2">
    <p>{{$user->id}}</p>
  </div>
  <div class="col-sm-6">
  </div>
</div>

<div class="row">
  <div class="col-sm-4">
    <button class="btn btn-info btn-med" onclick="window.location.href='/users/{{$user->id}}/edit'">Edit</button>
  </div>
  <div class="col-sm-8">
  </div>
</div>
<br>
@stop

@section('detailContent')
@if ($user->projectcreator)
<div id="api-info">
  <h3>API Access Information</h3>
  <p>Most operations may be performed via the CNP RESTful API - see documentation <a href="api-docs">here</a>. All API access requires the use of SSL and your API key in the Authorization header:</p>

  @if ($apikey = $user->getApiKey(true))
    <div id="api-key">
      <p>{{$apikey}}</p>
    </div>
  @else
    <div id="api-key"><input type="button" value="Get API Key"/>

    </div>
  @endif
</div>
@endif
<a href="/logout">Log Out</a>
</div>

@stop
