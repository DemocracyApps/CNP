@extends('layouts.default')

@section('content')
<h1>Account Page</h1>

<div>
<form method="POST" action="http://cnp.dev/projects" accept-charset="UTF-8">
  <div class="form-group">
    <label for="content">Notes: </label>
    <textarea class="form-control" name="content" cols="50" rows="10" id="content"></textarea>
  </div>
  <div>
    <input class="btn btn-primary" type="submit" value="Create">
  </div>
</form>
</div>

<div id="user-info">
<form method="POST" action="http://cnp.dev/stories" accept-charset="UTF-8">
  <div class="form-group">
    <label for="content">Notes: </label>
    <textarea class="form-control" name="content" cols="50" rows="10"/></textarea>
  </div>
  <div class="form-group">
    <input class="btn btn-primary" type="submit" value="Next">
  </div>
</form>
</div>



@stop
