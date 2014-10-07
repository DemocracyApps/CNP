@extends('layouts.default')

@section('content')

<div class="row">
  <div class="col-sm-6">
    <h1 style="display:inline;">
      Exploring: {{$project->name}}
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
  <li class="active"><a href="#search" role="tab" data-toggle="tab"><b>Select</b></a></li>
  <li><a href="#serendipity" role="tab" data-toggle="tab"><b>View</b></a></li>
  <li><a href="#share" role="tab" data-toggle="tab"><b>Share</b></a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active row" id="search">
    <br>
    <div class="row">
      <div class="col-sm-12">
        &nbsp;
        <div class="btn-group" data-toggle="buttons">
              <?php 
                $first = true;
              ?>
            @foreach ($types as $typeName => $typeCount)
              <?php
                $cls = "btn btn-info";
                if ($first) {
                  $cls .= " active";
                }
              ?>
              <label style="width:150px;" class="{{$cls}}">
                <input type="radio" name="type" value="{{$typeName}}"
                  @if ($first)
                    checked 
                  @endif
                >{{$typeName}} ({{$typeCount}})</input>
              </label>
              <?php 
                $first = false;
              ?>
            @endforeach
        </div>
      </div>
    </div>
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
