@extends('templates.default')

@section('title')
    My Notifications
@stop

@section('content')
    <ul class="nav nav-tabs">
        <li role="presentation"><a href="/user/profile">My Profile</a></li>
        <li role="presentation"><a href="/user/contributions">My Contributions</a></li>
        <li role="presentation" class="active"><a href="/user/notifications">My Notifications</a></li>
    </ul>

    <br>
    <h1>Notifications</h1>

    <table class="table table-striped">
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Status</th>
                <th>Messages</th>
            </tr>
        @foreach($notifications as $notification)
            <tr>
                <td> {!!  $notification->id  !!} </td>
                <td> {!!  $notification->type  !!} </td>
                <td> {!!  $notification->status !!} </td>
                <td> {!!  $notification->messages !!} </td>
            </tr>
        @endforeach
    </table>
    
@stop

