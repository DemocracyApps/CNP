@extends('layouts.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
  <li role="presentation"><a href="/system/elementtypes">Element Types</a></li>
  <li role="presentation"><a href="/system/relationtypes">Relation Types</a></li>
  <li role="presentation" class="active"><a href="/system/projects">Projects</a></li>
</ul>
  <h1>All Projects</h1>
  <table class="table">
    <tr>
      <th> ID </th>
      <th> Name </th>
      <th> Access </th>
      <th> User </th>
    </tr>
    @foreach ($projects as $project)
      <?php
        $user = \DemocracyApps\CNP\Entities\Eloquent\User::find($project->userid);
      ?>
      <tr>
        <td> {{ $project->id}} </td>
        <th> {{ link_to("admin/projects/".$project->id, $project->name) }} </th>
        <td> {{ $project->getProperty('access')}} </td>
        <td> {{$user->name}} </td>
      </tr>    
    @endforeach
  </table>s
@stop
