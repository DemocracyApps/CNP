
@extends('layouts.default_ext')

@section('title')
All Contributions to {{$project->name}}
@stop

@section('buttons')
    <button class="btn btn-warning" style="width:100px;" onclick="window.location.href='/{{$project->id}}'">Home</button>
@stop

@section('content')
    <br>
    <div class="row">
         <table class="table table-striped">
         @foreach($stories as $story)
         <tr>
         <td style="width:20%;"> {{ $story->id }} </td>
         <td style="width:70%;"> <a href="/compositions/{{$story->id}}">{{ $story->title}} </a></td>
         <td> {{$story->created_at}} </td>
         </tr>
         @endforeach
         </table>
         <br/>
         {{$stories->appends(\Request::except('page'))->links()}}
    </div>

@stop

@section('footer_right')
    @if ($owner)
        <a href="/projects/{{$project->id}}"> Project Page </a>
    @endif
@stop
