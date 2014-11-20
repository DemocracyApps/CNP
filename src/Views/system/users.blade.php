@extends('layouts.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation" class="active"><a href="/system/users">Users</a></li>
  <li role="presentation"><a href="/system/relationtypes">Relation Types</a></li>
</ul>
  <h1>Manage Users</h1>

  <table class="table">
    <tr>
      <th>User ID</th><th>User Name</th><th>Superuser?</th><th>Project Creator?</th><th>Last Login</th>
    </tr>
  </table>

@stop
