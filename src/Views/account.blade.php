@extends('layouts.default')

@section('content')
<h1>Account Page</h1>

<div id="user-info">
  <h2>User Information</h2>
  <table>
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
    @foreach($scapes as $scape)
    <li>{{ $scape->getId() }} {{ $scape->getName()}}</li>
  @endforeach
  </ul>
    <br/>
        {{ Form::open(['route' => 'scapes.create', 'method' => 'get']) }}
           <div>
             {{ Form::submit('Add a Scape') }}
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
