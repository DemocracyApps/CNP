@extends('layouts.default')

@section('content')
<h1>Upload CSV File of Stories</h1>

<p>We will use Collector specification {{ $collector->id }} ({{$collector->name}}) to process your stories.</p>
<br/>
{{ Form::open(['route' => array('stories.store'), 'files' => true]) }}
   {{ Form::hidden('collector', $collector->id)}}
   <div class="form-group">
      {{ Form::label('csv', 'CSV File')}}
      {{ Form::file('csv')}}
      
      <span class="error">{{ $errors->first('csv') }}</span>
   </div>
   <br/>
   <div class="form-group">
	  {{ Form::submit('Upload File', ['class' => 'btn btn-primary']) }}
   </div>
{{ Form::close() }}

@stop
