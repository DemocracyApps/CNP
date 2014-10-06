@extends('layouts.default')

@section('content')

<div class="row">
  <div class="col-sm-6">
    <h1 style="display:inline;">
      Explore Stories
    </h1>
  </div>
  <div class="col-sm-6" style="height:40px;" >
    <div class="col-xs-6">
      <b>Total:</b> &nbsp; {{$count}}
    </div>
    <div class="col-xs-6">
      <b>Selected:</b> &nbsp; {{$count}}
    </div>
<!--    <p style="position:absolute; bottom:0;">Some stuff</p>-->
  </div>
</div>


<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="#search" role="tab" data-toggle="tab"><b>Search</b></a></li>
  <li><a href="#serendipity" role="tab" data-toggle="tab"><b>Serendipity</b></a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active row" id="search">
    <div class="row">
      <div class="col-sm-6">
        <h4>
          Search 
        </h4>
        <br>
      </div>
      <div class="col-sm-3">
      </div>
    </div>
    <table class="table">
      @foreach ($types as $typeName => $typeCount)
        <tr>
          <td>{{$typeName}}:</td><td>{{$typeCount}}</td>
        </tr>
      @endforeach
    </table>
  </div>
  <div class="tab-pane row" id="serendipity">
  <div class="row">
      <div class="col-sm-6">
        <h4>
          Serendipity 
        </h4>
        <br>
      </div>
      <div class="col-sm-3">
      </div>
    </div>
    <p>And some serendipity stuff here</p>
  </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
  function getUrlParam(nm) {
    var params = window.location.search.substr(1).split("&");
    var result = null;
    for (var i=0; !result && params && i<params.length; ++i) {
      var pair = params[i].split("=");
      if (pair && pair[0].trim() == nm) {
        result = pair[1].trim();
      }
    }
    return result;
  }

</script>

@stop
