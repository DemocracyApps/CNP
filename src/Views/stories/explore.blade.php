@extends('layouts.tabs')

@section('title')
Explore Stories
@stop


@section('tabsContent')
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="#search" role="tab" data-toggle="tab"><b>Search</b></a></li>
  <li><a href="#serendipity" role="tab" data-toggle="tab"><b>Serendipity</b></a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active row" id="search">
    <div class="row">
      <div class="col-sm-6">
        <h1>
          Search 
        </h1>
        <br>
      </div>
      <div class="col-sm-3">
      </div>
    </div>
    <p>And some search stuff here</p>
  </div>
  <div class="tab-pane row" id="serendipity">
  <div class="row">
      <div class="col-sm-6">
        <h1>
          Serendipity 
        </h1>
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
  var scapeId = -1;

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
