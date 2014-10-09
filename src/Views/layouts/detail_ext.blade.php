@extends('layouts.default_ext')

@section('content')



<div class="row">
  <div class="col-sm-6"> 
    @yield('upperLeft')
  </div>
  <div class="col-sm-6"> 
    @yield('upperRight')
  </div>
</div>

@yield('detailContent')

@stop

@section('scripts')
  @yield ('listScripts')
@stop
</html>

