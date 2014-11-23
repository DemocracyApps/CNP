@extends('layouts.default')

@section('content')
<h1>Log In</h1>
<p>
  <a class="button" href="{{url('loginfb')}}"><i class="icon-facebook"></i> Login with Facebook</a>
</p>
<p>
  <a class="button" href="{{url('logintw')}}"><i class="icon-facebook"></i> Login with Twitter</a>
</p>

<p>
	<a class="button" href="{{url('logincheat')}}">Demo Login</a>
</p>
@stop
