@extends('layouts.default_demo')

@section('title')
What is the CNP?
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=data1';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <p><em> The Community Narratives Platform is a tool for capturing citizen input through stories at scale.</em></p>

    Three foundational ideas:
    <ul>
    <li>Connections, Not Collections
        <ul>
            <li>Unlock the structure within traditionally unstructured data</li>
        </ul>
    </li>
    <li>Catalyze the Unexpected
        <ul>
            <li>Discover surprising connections and answers to questions you didn't know to ask</li>
        </ul>
    </li>
    <li>Easy In, Easy Out
        <ul>
            <li>Make it easy to start and run a project and to reach people where they are.</li>
        </ul>
    </li>
  </ul>
</div>

@stop