@extends('layouts.default')

@section('content')
<h1>Create New Relation</h1>

{{ Form::open(['route' => 'relationtypes.store']) }}
   <div>
      {{ Form::label('name', 'Name of the Relation: ') }}
      {{ Form::input('text', 'name') }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <div>
      {{ Form::label('allowedfrom', 'Allowed FROM Element Types: ') }}
      {{ Form::input('text', 'allowedfrom') }}
      <span>&nbsp; Leave blank if no restriction. Separate multiple with commas.</span>
   </div>
   <div>
      {{ Form::label('allowedto', 'Allowed TO Element Types: ') }}
      {{ Form::input('text', 'allowedto') }}
      <span>&nbsp; Leave blank if no restriction. Separate multiple with commas.</span>
   </div>
   <div>
      {{ Form::label('inverseName', 'Inverse Relation Name: ') }}
      {{ Form::input('text', 'inverseName') }}
      <span>&nbsp; Leave blank if relation is symmetric.</span>
   </div>
   <div>
	{{ Form::submit('Create the Relation Type') }}
   </div>
{{ Form::close() }}

@stop
