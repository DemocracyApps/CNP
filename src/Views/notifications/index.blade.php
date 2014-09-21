@extends('layouts.default')

@section('content')
<h1>Notifications</h1>

	<table class="table table-striped">
    @foreach($notifications as $notification)
      <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Status</th>
        <th>Messages</th>
      </tr>
      <tr>
        <td> {{ $notification->id }} </td> 
        <td> {{ $notification->type }} </td>
        <td> {{ $notification->status}} </td>
        <td> {{ $notification->messages}} </td>
      </tr>
    @endforeach
	</table>
@stop
