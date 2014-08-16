@extends('layouts.default')

@section('content')
<h1>HOME</h1>

<p> Hi, there {{Auth::user()->getUserName()}}</p>
<br/>
<p>
  <a class="button" href="{{url('logout')}}"> Log Out</a>
</p>


@stop

