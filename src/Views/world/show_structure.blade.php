@extends('layouts.detail_ext')

@section('buttons')
    <button class="btn btn-warning" style="width:100px;" onclick="window.location.href='/{{$project}}'">Home</button>
@stop

@section('title')
{{ $story->name }}
@stop

@section('upperLeft')
<div class="row">
    <p><b>Element ID:</b>  &nbsp; {{$story->id}}</p>
</div>

@stop

@section('upperRight')

  <!-- Decorators section start -->
  <!-- See Laravel template section on how to pass parameters to an include. 
   -->
<div class="row">
    <p><a href="/{{$project}}/compositions/{{$composition->id}}?view=normal">Back to Normal View</a></p>
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
          <td><a href="/{{$project}}/compositions/{{$composition->id}}?view=structure&element={{$element->id}}">{{$element->id}}</a></td>
          <td>{{$element->name}}</td>
          <td style="width:50%;">{{$element->content}} </td>
          <td>
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
