
@extends('layouts.default_ext')

@section('title')
Perspectives
@stop

@section('buttons')
    <button class="btn btn-warning" style="width:100px;" onclick="window.location.href='/{{$project->id}}'">Home</button>
@stop

@section('content')
    <div class="row">
        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in tortor ullamcorper, sodales enim quis, vehicula dui. Nulla faucibus dolor sit amet enim rhoncus rutrum. Aenean iaculis volutpat tellus, eget vulputate erat dictum ut. Vestibulum mollis elit ut risus facilisis, mattis venenatis metus iaculis. Fusce sed cursus sem, nec ornare erat. </p>
        <br>
    </div>
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;" onclick="window.location.href='/{{$project->id}}/compositions'">Top Tags</button>
                    </div>
                </div>
                <br>
                <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words.</p>
            </div>
            <div class="col-md-2">
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;">Surprise Me</button>
                    </div>
                </div>
                <br>
                <p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p>
            </div>
            <div class="col-md-1">
            </div>
        </div>
@stop

@section('footer_right')
    @if ($owner)
        <a href="/admin/projects/{{$project->id}}"> Project Page </a>
    @endif
@stop