@extends('layouts.default_demo')

@section('title')
The Power of Story
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=story2';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <p><em> Ordinary people are able to convey complex, interesting and valuable information<br> 
         when they can present it in a form that is natural for them.</em></p>

    Stories:
    <ul>
        <li>Are an efficient way to store and convey rich information</li>
        <li>Allow for the unexpected</li>
        <li>Are sticky and memorable</li>
        <li>Address peoples' need to be listened to</li>
    </ul>
</div>

@stop
