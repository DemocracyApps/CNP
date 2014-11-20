@extends('layouts.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
  <li role="presentation" class="active"><a href="/system/relationtypes">Relation Types</a></li>
</ul>
  <h1>Available RelationTypes</h1>

  <table class="table">
    <tr>
      <th>ID</th><th>Name</th><th>Inverse</th>
    </tr>
    @foreach($relationtypes as $relationtype)
      <tr>
        <td> {{ $relationtype->id }} </td>
        <td> {{ $relationtype->name}} </td>
        <td> {{ $relationtype->inverse}} </td>
      </tr>
    @endforeach
  </table>
  <br/>
  {{ Form::open(['route' => 'system.relationtypes.create', 'method' => 'get']) }}
     <div>
       {{ Form::submit('Add a Relation Type') }}
     </div>
  {{ Form::close() }}
@stop
