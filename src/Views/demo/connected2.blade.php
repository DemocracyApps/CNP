@extends('layouts.default_demo')

@section('title')
The Connected Story 
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=easy1';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <p> 9,233 Elements &nbsp;&nbsp; - &nbsp;&nbsp; 54,118 Relations</p>
    <div class="col-sm-12">
    <img align="middle" src="/img/kumu3.png"  height="554" width="632" alt="Story Network"/>
  </div>
</div>

@stop
