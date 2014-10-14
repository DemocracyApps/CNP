@extends('layouts.default_demo')

@section('title')
Demo Data & Questions
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=connected1';">Next</button>
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

    <ul class="small">
<li>  Q2: Please tell a story about a time when a person or an organization tried to help someone or change something in your community.</li>

<li>   Q3: Give your story a title.</li>

<li>   Q4: Name the organization or group most involved in what happened.</li>

<li>   Q5: What is this story about?</li>

<li>   Q7: Where did this story take place? (city or district)</li>

<li>   Q12: What is your connection to what happened in the story (saw/affected by/etc.)?</li>

<li>   Q13: Who benefitted from what happened in the story (right/wrong people)?</li>

<li>   Q19: Which of these relate to your story (topic areas)?</li>

</ul>
    <img align="middle" src="/img/table.png"  alt="table"/>

</div>

@stop
