@extends('layouts.detail')

@section('title')
Story Curation
@stop

@section('buttons')

<!-- View Stories Button -->
{{ Form::open(array('route' => array('stories.index'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-info btn-sm"><b>View Stories</b></button>
{{ Form::close() }}

@stop

@section('upperLeft')
<p> Something here </p>
@stop

@section('upperRight')
<p> Something here </p>

@stop


@section('detailContent')
<hr/>
<br>

<p>And some other stuff here</p>

@stop
