@extends('layouts.default')

@section('content')
    <h1>Email change</h1>

    <p>You should receive an email shortly inviting you to confirm your change. Confirmation is required
        in order to post content or to participate in private projects.</p>
    <br>

    {{ Form::open(['route' => array('signup.thanks'),
                   'method' => 'post']) }}

    <div class="form-group">
        <input name="submit" type="submit" style="width:200px;" class='btn btn-primary' value="Continue">
    </div>

    {{ Form::close() }}

@stop