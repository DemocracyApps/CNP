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
        <li>Getting stories
            <ul>
                <li> CSV upload and auto-generated web form so far</li>
                <li> Extension to FB, SMS, mobile web is easy, plus API for extensions</li>
                <li> Everything currently is text, but trivial to extend to video and pics.</li>
            </ul>
        </li>
        <li>Presenting stories
            <ul>
                <li> Rudimentary output system for now, but already follows key principles:<br>reasonable output with no effort and decent output with minimal effort. </li>
            </ul>
        </li>
        <li>Stories of stories
            <ul>
                <li>One of the most exciting tasks: enable ordinary people to identify <br>patterns within a collection of stories and share that back with the community.</li>
            </ul>
        </li>
    </ul>

</div>

@stop
