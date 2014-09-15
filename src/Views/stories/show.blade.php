@extends('layouts.default')

@section('content')
<h1>{{ $story->getName() }} </h1>


<div>
  <h3>Story Information</h3>
  <table>
    <tr>
      <th style="width:30%;">ID:</th>  <td style="width:70%;">{{$story->getId()}}</td>
    </tr>
    <tr>
      <th>Scape:</th>  <td>{{$story->scapeId}}</td>
    </tr>
    <tr>
      <th>Content:</th>  <td>{{$story->getContent()}}</td>
    </tr>
    <tr>
      <th>Branch:</th>  <td><a href="/stories/create?composer=7&referent={{$story->getId()}}">Fly!</a></td>
    </tr>
  </table>

  <br/>

  <!-- Decorators section start -->
  <!-- See Laravel template section on how to pass parameters to an include. 
   -->
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
      <li><a href="/stories/create?composer={{$decorator}}&referent={{$story->getId()}}">{{$launchText}}</a></li>
    @endforeach
    </ul>
  @endif
  <!-- Decorators section end -->

  <br/>

  @if (sizeof($elements) > 0)
    <h3>Story Elements</h3>
    <table>
      <th style="width:12%;">Name</th><th>Content</th><th style="width:30%;">Relations</th>
      @foreach ($elements as $element)
        <tr>
          <td>{{$element->name}} ({{$element->id}})</td>
          <td>{{$element->content}} </td>
          <td>
            <table style="border:none;" class="simple-table">
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
