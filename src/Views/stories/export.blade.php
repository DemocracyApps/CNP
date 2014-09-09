@extends('layouts.default')

@section('content')

<h1>Export Stories from Scape {{$scape}}</h1>
<br/>
<br/>

{{ Form::open(array('route' => array('kumu'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <input type="hidden" name="scape" value="{{$scape}}"/>
  <button type="submit" class="btn btn-info btn-mini">Export to Kumu</button>
{{ Form::close() }}

<br/>
<br/>

@stop
