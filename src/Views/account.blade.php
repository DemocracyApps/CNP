@extends('layouts.detail')

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
    <p><b>Email:</b></p>
  </div>
  <div class="col-sm-2">
    <p>{{$user->email}}</p>
  </div>
  <div class="col-sm-6">
  </div>
</div>
@stop

@section('detailContent')

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
<a href="/logout">Log Out</a>
</div>

@stop
