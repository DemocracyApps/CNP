@extends('layouts.default')

@section('content')
<h1>We Have the Stories</h1>

<div class="menu">
  <div class="menu-inner">
    <ul class="menu">
      <li class="menu"><a href="/stories/create?type=mass">Upload Stories</a></li>
        <li class="menu"><a href="/stories/create?type=single&spec=a223">Single Story</a></li>
    </ul>
  </div>
</div>


	<ul>
	@foreach($stories as $story)
		<li>{{ $story->getId() }} {{ $story->getName()}}</li>
	@endforeach
	</ul>
    <br/>
        {{ Form::open(['route' => 'stories.create', 'method' => 'get']) }}
           <div>
             {{ Form::submit('Add a Story') }}
           </div>
        {{ Form::close() }}
        <?php
        \Log::info("Bottom of view make");
        ?>
@stop
