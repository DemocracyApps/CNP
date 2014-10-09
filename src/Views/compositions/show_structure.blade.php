@extends('layouts.detail')

@section('title')
{{ $composition->title }}
@stop

@section('upperLeft')
<div class="row">
  <div class="col-sm-4">
    <p><b>Story ID:</b></p>
  </div>
  <div class="col-sm-8">
    <p>{{$composition->id}}</p>
  </div>
</div>
<div class="row">
  <div class="col-sm-4">
    <p><b>Project ID:</b></p>
  </div>
  <div class="col-sm-2">
    <p>{{$composition->project}}</p>
  </div>
  <div class="col-sm-6">
  </div>
</div>
<div class="row">
  <div class="col-sm-4">
    <p><b>Branch:</b></p>
  </div>
  <div class="col-sm-2">
    <p><a href="/compositions/create?composer=5&referent={{$story->getId()}}">Create Branch</a></p>
  </div>
  <div class="col-sm-6">
  </div>
</div>
@stop

@section('upperRight')

  <!-- Decorators section start -->
  <!-- See Laravel template section on how to pass parameters to an include. 
   -->
<div class="row">
    <p><a href="/compositions/{{$composition->id}}?view=normal">Normal View</a></p>
</div>

<div class="row">
  <p><b>Branches:</b></p>
</div>
<div class="row">

  @if ($story->hasProperty('branchDecorators'))
    <?php
        $decorators = explode(',', $story->getProperty('branchDecorators'));
    ?>
    <ul>
    @foreach ($decorators as $decorator)
      <?php
      $launchText = null;
        $composer = \DemocracyApps\CNP\Compositions\Composer::find($decorator);
        if ($composer && $composer->validForInput()) {
          $launchText = $composer->getInputProperty('referentLaunchText');
        }
        if ($launchText == null) $launchText = "Branch to Comment";
      ?>
      <li><a href="/compositions/create?composer={{$decorator}}&referent={{$story->getId()}}">{{$launchText}}</a></li>
    @endforeach
    </ul>
  @else
    <p>No branches</p>
  @endif
  <!-- Decorators section end -->
</div>

@stop

@section('detailContent')


  @if (sizeof($elements) > 0)

    <h3>Story Elements</h3>
    <table class="table">
      <td>ID</td>
      <td >Name</td>
      <td>Content</td>
      <td >Relations</td>

      @foreach ($elements as $element)
        <tr>
          <td>{{$element->id}}</td>
          <td>{{$element->name}}</td>
          <td>{{$element->content}} </td>
          <td>
            <?php
              \Log::info("Element " . $element->id . " - " . $element->name);
            ?>
            <table style="border:none;" class="table simple-table">
              @foreach ($relations[$element->id] as $rel)
                <tr>
                  <td style="border:none;"> {{ $rel[0] }} </td>
                  <td style="border:none;"> {{ $rel[1] }} </td>
                </tr>
              @endforeach
            </table>
          </td>
        </tr>
      @endforeach
    </table>
  @endif
</div>

@stop