@extends('layouts.default')

@section('content')

<h1>Export Stories from Project {{$project}}</h1>
<br/>
<br/>

{{ Form::open(array('route' => array('kumu'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="project" value="{{$project}}"/>
  <button type="submit" class="btn btn-info btn-mini">Export to Kumu</button>
{{ Form::close() }}

<br/>
<br/>

@stop
