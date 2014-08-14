@extends('layouts.default')

@section('content')
<h1>HOME</h1>

<p> Hi, there {{Auth::user()->getUserName()}}</p>
@stop

