@extends('layouts.default_demo')

@section('title')
The Connected Story 
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=easy1';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <div class="col-sm-12">
    <img align="middle" src="/img/net2.png"  height="554" width="632" alt="Story Network"/>
  </div>
</div>

@stop
