@extends('templates.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
   <li role="presentation"><a href="/system/elementtypes">Element Types</a></li>
  <li role="presentation" class="active"><a href="/system/relationtypes">Relation Types</a></li>
  <li role="presentation"><a href="/system/projects">Projects</a></li>
</ul>
<h1>Create New Relation</h1>

{!!  Form::open(['route' => 'system.relationtypes.store'])  !!}
   <div>
      {!!  Form::label('name', 'Name of the Relation: ')  !!}
      {!!  Form::input('text', 'name')  !!}
      <br/>
      <span class="error">{!!  $errors->first('name')  !!}</span>
   </div>
   <div>
      {!!  Form::label('allowedfrom', 'Allowed FROM Element Types: ')  !!}
      {!!  Form::input('text', 'allowedfrom')  !!}
      <span>&nbsp; Leave blank if no restriction. Separate multiple with commas.</span>
   </div>
   <div>
      {!!  Form::label('allowedto', 'Allowed TO Element Types: ')  !!}
      {!!  Form::input('text', 'allowedto')  !!}
      <span>&nbsp; Leave blank if no restriction. Separate multiple with commas.</span>
   </div>
   <div>
      {!!  Form::label('inverseName', 'Inverse Relation Name: ')  !!}
      {!!  Form::input('text', 'inverseName')  !!}
      <span>&nbsp; Leave blank if relation is symmetric.</span>
   </div>
   <div>
	{!!  Form::submit('Create the Relation Type')  !!}
   </div>
{!!  Form::close()  !!}

@stop
