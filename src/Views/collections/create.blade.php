@extends('layouts.default')

@section('content')
<h1>Create New Collection</h1>

{{ Form::open(['route' => 'collections.store']) }}
   <div class="form-group">
      {{ Form::label('name', 'Name: ') }}
      {{ Form::text('name', null, ['class' => 'form-control']) }}
      <br/>
      <span class="error">{{ $errors->first('name') }}</span>
   </div>
   <br/>
   <div class="form-group">
      {{ Form::label('project', 'Project: ') }}

      <select id="project" name="project" class="form-control">
          @foreach ($projects as $project)
            <option value="{{$project->id}}">{{$project->name}}</option>
          @endforeach
      </select>
   </div>
   <br/>
   <div>
	   {{ Form::submit('Create', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
