@extends('templates.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
   <li role="presentation" class="active"><a href="/system/elementtypes">Element Types</a></li>
  <li role="presentation"><a href="/system/relationtypes">Relation Types</a></li>
  <li role="presentation"><a href="/system/projects">Projects</a></li>
</ul>
<h1>Create New Element Type</h1>

{!!  Form::open(['route' => 'system.elementtypes.store'])  !!}
   <div>
      {!!  Form::label('name', 'Name of the Element Type: ')  !!}
      {!!  Form::input('text', 'name')  !!}
      <br/>
      <span class="error">{!!  $errors->first('name')  !!}</span>
   </div>
   <div>
	{!!  Form::submit('Create the New Type')  !!}
   </div>
{!!  Form::close()  !!}

@stop
