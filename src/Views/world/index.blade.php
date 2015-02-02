
@extends('layouts.default_ext')

@section('title')
    @if ($filterDescription != null)
        {{$filterDescription. " in " . $project->name}}
    @else
        All Contributions in {{$project->name}}
    @endif
@stop

@section('buttons')
    @if ($filterDescription != null)
        <button class="btn btn-warning" style="width:100px;" onclick="window.location.href='/{{$project->id}}/compositions'">All Stories</button>
    @endif
    <button class="btn btn-warning" style="width:100px;" onclick="window.location.href='/{{$project->id}}'">Project Home</button>
@stop

@section('content')
    <br>
    @if ($advancedView)
        <p>{{$advancedViewMessage}}</p>
        <br>
    @endif
    <div class="row">
         <table class="table table-striped">
            <?php
                // Title
                if ($sort == 'title') $ndesc = !$desc;
                else $ndesc = false;
                $titleSort = $ndesc?'true':'false';
                // Author
                if ($sort == 'user') $ndesc = !$desc;
                else $ndesc = false;
                $userSort = $ndesc?'true':'false';
                // Date
                if ($sort == 'date') $ndesc = !$desc;
                else $ndesc = true;
                $dateSort = $ndesc?'true':'false';

            ?>

            <th><a href="/{{$project->id}}/compositions?sort=title&desc={{$titleSort}}">Title</a></th>
            <th><a href="/{{$project->id}}/compositions?sort=user&desc={{$userSort}}">User</a></th>
            <th><a href="/{{$project->id}}/compositions?sort=date&desc={{$dateSort}}">Date</a></th>
             @foreach($stories as $story)
                 <tr>
                     <td style="width:60%;"> <a href="/{{$project->id}}/compositions/{{$story->id}}">{{ $story->title}} </a></td>
                     <td style="width:20%;"> {{ $story->name }} </td>
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
        <a href="/admin/projects/{{$project->id}}"> Project Page </a>
    @endif
@stop
