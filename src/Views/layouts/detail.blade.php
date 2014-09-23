@extends('layouts.default')

@section('content')
<!--
<div class="row">
  <div class="col-sm-1"><p> 1 </p></div>
  <div class="col-sm-1"><p> 2 </p></div>
  <div class="col-sm-1"><p> 3 </p></div>
  <div class="col-sm-1"><p> 4 </p></div>
  <div class="col-sm-1"><p> 5 </p></div>
  <div class="col-sm-1"><p> 6 </p></div>
  <div class="col-sm-1"><p> 7 </p></div>
  <div class="col-sm-1"><p> 8 </p></div>
  <div class="col-sm-1"><p> 9 </p></div>
  <div class="col-sm-1"><p> 10 </p></div>
  <div class="col-sm-1"><p> 11 </p></div>
  <div class="col-sm-1"><p> 12 </p></div>
</div>
-->
<div class="row">
  <div class="col-sm-6">
    <h1>
      @yield('title')
    </h1>
  </div>
</div>
<div class="row">
  <div class="col-sm-6">
  </div>
  <div class="col-sm-6" style="margin-bottom:10px;">
    <div style="float:right; position:absolute; bottom:0; right:0;"
      @yield('buttons')
    </div>
  </div>
</div>

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

