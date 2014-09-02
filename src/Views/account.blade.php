@extends('layouts.default')

@section('content')
<h1>Account Page</h1>

<div id="user-info">
  <h2>User Information</h2>
  <table cellpadding="10">
    <tr>
      <th>Name:</th>  <td>{{$user->name}}</td>
    </tr>
    <tr>
      <th>Email:</th>  <td>{{$user->email}}</td>
    </tr>
  </table>
</div>

<div id="scape-list">
  <h2>Your Scapes</h2>
  <table>
    <tr>
      <th> Name </th>
      <th> Access </th>
      <th> Content </th>
      <th> ID </th>
    @foreach($scapes as $scape)
      <tr>
        <th> {{ link_to("scapes/".$scape->getID(), $scape->getName()) }} </th>
        <td> {{ $scape->getProperty('access') }} </td>
        <td> {{ $scape->getContent() }} </td>
        <td> {{ $scape->getId() }} </td>
      </tr>
    @endforeach
  </table>
  <br/>
        {{ Form::open(['route' => 'scapes.create', 'method' => 'get']) }}
           <div>
             {{ Form::submit('Add a Scape',['class' => 'btn btn-info']) }}
           </div>
        {{ Form::close() }}

</div>
<div id="api-info">
  <h2>API Access Information</h2>
  <p>Most operations may be performed via the CNP RESTful API - see documentation <a href="api-docs">here</a>. All API access requires the use of SSL and your API key in the Authorization header:</p>
  @if ($apikey = $user->getApiKey(true))
    <div id="api-key"><p>{{$apikey}}</p></div>
  @else
    <div id="api-key"><input type="button" value="Get API Key"/></div>
  @endif
</div>

@stop
