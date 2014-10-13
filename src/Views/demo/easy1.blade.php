@extends('layouts.default_demo')

@section('title')
Easy In, Easy Out
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=story2';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <p><em> A key goal of the CNP is to empower civic engagement by people who have traditionally been excluded.</em></p>

    Two key principles:
    <ul>
        <li>Show up where people are</li>
        <li>Make it usable by people with ordinary digital skills</li>
    </ul>

    <p>We are only beginning, but we've taken some crucial initial steps</p>

    <ul>
        <li>Getting stories<br>CSV upload and auto-generated web form for now, but extension to FB, SMS,<br>mobile web are easy, plus API for extensions. Everything we're doing now is text, <br>but trivial to extend to video and pics.</li>
        <li>Presenting stories - Rudimentary output system for now, but it already gives reasonable <br>output with no effort and decent output with minimal effort. </li>
        <li>Stories of stories - One of the most exciting tasks: enable ordinary people to identify <br>patterns within a collection of stories and share that back with the community.</li>
    </ul>

</div>

@stop
