
@extends('layouts.default_ext')

@section('content')
    <div class="row">
        <h1>Welcome to {{$project->name}}!</h1>
        <p> {{$project->description}} </p>
        <br>
    </div>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;" onclick="window.location.href='/{{$project->id}}/compositions/create?composer={{$defaultInputComposer}}'">Share Your Story</button>
                    </div>
                </div>
                <br>
                <p>Cras ante neque, fringilla eget libero non, maximus congue justo. Quisque dictum ligula in lacinia dignissim. Duis aliquam ullamcorper metus vitae bibendum. Ut consectetur dignissim laoreet. Ut convallis faucibus ultricies. Quisque vel nunc condimentum metus vulputate efficitur. Duis laoreet nunc quis justo lacinia porttitor. In ornare nisi et placerat vehicula. Pellentesque dapibus eget felis et aliquam. Sed aliquet velit nec accumsan pellentesque. Curabitur vehicula vitae libero non malesuada. In commodo ex nunc, sit amet dictum est pharetra vel. </p>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;" onclick="window.location.href='/{{$project->id}}/compositions'">List of Stories</button>
                    </div>
                </div>
                <br>
                <p>TQuisque libero mi, consequat in hendrerit at, tristique quis massa. Nullam urna risus, mattis vel commodo vel, tincidunt vitae turpis. Praesent auctor sed leo in sodales. In sed finibus lacus, sit amet vestibulum urna. Phasellus auctor odio nulla, id tempus leo fringilla ac. Curabitur pharetra nulla libero, quis placerat leo hendrerit ut. Aliquam pulvinar, est et hendrerit ultricies, diam sem congue massa, nec dictum risus mi a purus. Aenean hendrerit elementum lectus, vitae rutrum purus tincidunt eget. Praesent finibus non elit sit amet fermentum.</p>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;" onclick="window.location.href='{{$project->id}}/sos_start'">Perspectives</button>
                    </div>
                </div>
                <br>
                <p>Donec maximus ligula nisl, eu rutrum quam luctus et. Ut ullamcorper, tortor et suscipit placerat, mauris felis aliquam lectus, a molestie massa tortor id nisi. Maecenas dignissim eros eget malesuada aliquam. Cras aliquet diam ac quam facilisis consectetur. Mauris laoreet orci mauris, in laoreet orci efficitur eget. Fusce massa turpis, hendrerit ac mattis non, eleifend vel sem. Nam quam eros, posuere id pellentesque sed, porta quis tellus.</p>
            </div>
        </div>
@stop

@section('footer_right')
    @if ($owner)
        <a href="/admin/projects/{{$project->id}}"> Project Page </a>
    @endif
@stop