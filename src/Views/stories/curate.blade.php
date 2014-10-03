@extends('layouts.detail')

@section('title')
Story Curation
@stop

@section('buttons')

<!-- View Stories Button -->
{{ Form::open(array('route' => array('stories.index'), 'method' => 'get', 
                                            'style' => 'display:inline-block')) }}
  <button type="submit" class="btn btn-info btn-sm"><b>View Stories</b></button>
{{ Form::close() }}

@stop

@section('upperLeft')
<h3> Summary </h3>


<div class="row">
  <div class="col-sm-4">
    <p><b>Project:</b></p>
  </div>
  <div class="col-sm-2">
    @if (isset($project))
      <?php
        $project = \DemocracyApps\CNP\Entities\Project::find($project);
      ?>
      <p>{{$project->name}}</p>
    @else
      <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
        Select Project
      </button>
    @endif
  </div>
  <div class="col-sm-6">
  </div>
</div>

<!-- Popup div for modal project (project) selection -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Select Project</h4>
      </div>
      <?php
              $projects = \DemocracyApps\CNP\Entities\Project::allUserElements(\Auth::id());
      ?>
      <div class="modal-body"> 
        <select id="projectselect">
          <option value="-1"> --- </option>
          @foreach ($projects as $s) 
            <option value="{{$s->id}}">{{$s->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal"
            onclick="go()">Save</button>
      </div>
    </div>
  </div>
</div>

@stop

@section('upperRight')
<h3>Options</h3>

@if (isset($project))

  <div class="row">
    <div class="col-sm-4">
      <p><b>Selected Templates:</b></p>
    </div>
    <div class="col-sm-5">
      <p id="selectedTemplatesString"> 
        @if (isset($selectedComposers))
          {{$selectedComposers}}
        @endif
      </p>
    </div>
    <div class="col-sm-3">
      <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal2">
        Edit
      </button>
    </div>
  </div>


<!-- Div for modal template (composer) selection -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Select Template</h4>
      </div>
      <div class="modal-body"> 
        <?php
          $templates = array();
          if (isset($selectedComposers)) {
            $tmp = explode(",", $selectedComposers);
            foreach ($tmp as $item) {
              $templates[$item] = true;
            }
          }
        ?>
        <form>
          @foreach ($composers as $c) 
            <?php
              $checked = "";
              if (array_key_exists($c->id, $templates)) {
                $checked = "checked";
              }
            ?>
            <input class="composerSelect" type="checkbox" name="project" 
                   value="{{$c->id}}" {{$checked}}>{{$c->name}} 
            <br>
          @endforeach
        </form> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal"
            onclick="setComposers()">Save</button>
      </div>
    </div>
  </div>
</div>

@else
  <p>This is it.</p>
@endif

@stop


@section('detailContent')
<hr/>
<br>

<p>And some other stuff here</p>

@stop

@section('scripts')
<script type="text/javascript">
  var projectId = -1;

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


  function setComposers() {
    var list = $( ".composerSelect" );
    s = "";
    for (var i=0; i<list.length; ++i) {
      if (list[i].checked) {
        if (s.length > 0) s += ",";
        s += list[i].value;
      }
    }
    var p = $( "#selectedTemplatesString");
    p[0].innerHTML = s;
    projectId = getUrlParam("project");
    var location = "http://cnp.dev/stories/curate?project=" + projectId + "&templates="+s;
    window.location.href=location;

    //document.getElementById("selectedTemplatesString").innerHTML = s;
  }

  function doit(event, ui) {
    projectId = $( "#projectselect" ).val();
  }
  function go() {
    var location = "http://cnp.dev/stories/curate?project=" + projectId;
    if (projectId > 0) {
      window.location.href=location;
    }
  }

  function processInputTemplates() {
    alert("Got it! Value is " + $("#inputTemplates").val());
@if (isset($project))
    var source ="http://cnp.dev/ajax/curate?project={{$project->id}}&composers="+$("#inputTemplates").val();
@else
    var source = " ";
@endif
    $.get( source, function( r ) {
      alert("Got r = " + r);
    });
  }
  $( "select" ).change( doit );

  $("#inputTemplates").change (processInputTemplates);

</script>

@stop
