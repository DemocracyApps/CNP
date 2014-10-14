@extends('layouts.default_demo')

@section('title')
The Power of Story
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=cnp1';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <p><em> Anyone is able to convey complex, interesting and valuable information when they can<br> 
          present it in a form that is natural for them.</em></p>

    Stories:
    <ul>
        <li>Are an efficient way to store and convey rich information</li>
        <li>Are sticky and memorable</li>
        <li>Address peoples' need to be listened to</li>
    </ul>
    <p>But there are practical problems with stories.</p>
    <ul>
        <li>Small numbers - Just anecdotal evidence</li>
        <li>Large numbers - Large amounts of unstructured data</li>
    </ul>
</div>

@stop
