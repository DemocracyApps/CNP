@extends('layouts.default_demo')

@section('title')
The Business
@stop
@section('buttons')
<button class="btn btn-warning" onclick="window.location.href='/demo?stage=next1';">Next</button>
@stop
@section('content')
<div class="presentation container">
    <p><em>We are committed to building a sustainable, hopefully profitable business.</em></p>

Likely potential users (project creators):
    <ol>
        <li>Governments interested in new ways of listening to citizens</li>
        <li>Civic associations (e.g., neighborhood groups) that would like to be heard</li>
        <li>Advocacy groups & non-profits interested in getting new voices heard</li>
        <li>Media organizations interested in the stories</li>
        <li>Businesses undertaking projects with significant community impact</li>
        <li>Corporations interested in improving what they can do with traditional surveys</li>
    </ol>

Current thinking:
    <ul>
        <li>Revenue potential*:
            <ul>
                <li>Probably has to be free for 2</li>
                <li>We can charge for 1, 3 and 4, but unlikely to sustain the business at the scale we hope</li>
                <li>We do think 5 and 6 have real potential.</li>
            </ul>
        </li>
        <li>Short-term: Focus on grants and investment</li>
        <li>Mid-term: Pursue #5 above - well-aligned with our core purpose</li>
        <li>Long-term: Commercial spin-out for #6 that licenses the technology</li>
    </ul>

    <p>*Code is open-source (<a href="https://github.com/DemocracyApps/CNP">github.com/DemocracyApps/CNP</a>). Platform would be sold as a service.</p>
</div>

@stop
