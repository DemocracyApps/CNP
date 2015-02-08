@extends('templates.default')

@section('content')
    <h1>Email change</h1>

    <p>You should receive an email shortly inviting you to confirm your change. Confirmation is required
        in order to post content or to participate in private projects.</p>
    <br>

    <form method="POST" action="{!! url('user') . '/' . 'email_changed' !!}">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <div class="form-group">
            <input name="submit" type="submit" style="width:200px;" class='btn btn-primary' value="Continue">
        </div>

    </form>
@stop