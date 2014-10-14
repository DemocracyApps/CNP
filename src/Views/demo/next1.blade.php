@extends('layouts.default_demo')

@section('title')
Next Steps
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=value';">Start Over</button>
@stop
@section('content')
<div class="presentation container">
    <p><em>We are looking for projects, connections, resources and advice.</em></p>

Current next steps:
    <ul>
        <li>Smaller-scale story collection project in Asheville, partner with Asheville Citizen-Times</li>
        <li>In conversation with a local non-profit about a community listening project</li>
        <li>In conversation with a major city about a community listening project</li>
        <li>Plan to apply again for the Knight Foundation Prototype Grant</li>
    </ul>


</div>

@stop
