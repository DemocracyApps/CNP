@extends('layouts.default_demo')

@section('title')
Demo Data & Questions
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=data2';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <p><em>Demo questions and data inspired and provided by Global Giving</em></p>
    <ul>
        <li>Fundraising organization supporting international community development</li>
        <li>Using stories for program evaluation</li>
        <li>57,000 stories collected ... and available online</li>
        <li>Field guide offers tremendous insight and ideas</li>
    </ul>
</div>

@stop
