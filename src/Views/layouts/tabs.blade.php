@extends('layouts.default')

@section('content')

<div class="row">
  <div class="col-sm-9">
  </div>
  <div class="col-sm-3" style="margin-bottom:10px;">
    <div style="float:right;">
      @yield('buttons')
    </div>
  </div>
</div>

@yield('tabsContent')

@stop

@section('scripts')
  @yield ('listScripts')
@stop
</html>

