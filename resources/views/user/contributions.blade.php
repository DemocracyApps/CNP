@extends('layouts.default_ext')

@section('title')
    My Contributions
@stop

@section('content')
    <ul class="nav nav-tabs">
        <li role="presentation"><a href="/user/profile">My Profile</a></li>
        <li role="presentation" class="active"><a href="/user/contributions">My Contributions</a></li>
    </ul>

    <br>
    <div class="row">
        <table class="table table-striped">
            <?php
            // Title
            if ($sort == 'title') $ndesc = !$desc;
            else $ndesc = false;
            $titleSort = $ndesc?'true':'false';
            // Author
            if ($sort == 'project') $ndesc = !$desc;
            else $ndesc = false;
            $projectSort = $ndesc?'true':'false';
            // Date
            if ($sort == 'date') $ndesc = !$desc;
            else $ndesc = true;
            $dateSort = $ndesc?'true':'false';

            ?>

            <th><a href="/user/contributions?sort=title&desc={{$titleSort}}">Title</a></th>
            <th><a href="/user/contributions?sort=user&desc={{$projectSort}}">Project</a></th>
            <th><a href="/user/contributions?sort=date&desc={{$dateSort}}">Date</a></th>
            @foreach($stories as $story)
                <tr>
                    <td style="width:60%;"> <a href="/{{$story->project}}/compositions/{{$story->id}}">{{ $story->title}} </a></td>
                    <td style="width:20%;"> <a href="/{{$story->project}}">{{ $story->projectName }} </a></td>
                    <td> {{$story->created_at}} </td>
                </tr>
            @endforeach
        </table>
        <br/>
        {{$stories->appends(\Request::except('page'))->links()}}
    </div>



@stop

