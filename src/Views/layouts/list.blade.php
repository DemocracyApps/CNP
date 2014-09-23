@extends('layouts.default')

@section('content')

<div class="row">
  <div class="col-sm-6">
    <h1>
      @yield('title')
    </h1>
    <br>
  </div>
  <div class="col-sm-3">
  </div>
</div>
<div class="row">
  <div class="col-sm-9">
  </div>
  <div class="col-sm-3" style="margin-bottom:10px;">
    <div style="float:right;">
      @yield('buttons')
    </div>
  </div>
</div>

<div class="row">
  @yield('listContent')
</div>

@stop

@section('scripts')
  @yield ('listScripts')
@stop
</html>

