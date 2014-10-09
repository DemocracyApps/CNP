
@extends('layouts.default_ext')

@section('content')
    <div class="row">
        <h1>Welcome to {{$project->name}}!</h1>
        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in tortor ullamcorper, sodales enim quis, vehicula dui. Nulla faucibus dolor sit amet enim rhoncus rutrum. Aenean iaculis volutpat tellus, eget vulputate erat dictum ut. Nunc facilisis nisl erat, sed ornare libero lobortis at. Vestibulum eu elementum sem, nec ornare augue. Curabitur sagittis tellus at ante congue ultrices. Sed vel sagittis metus. Sed convallis, sapien eu fermentum eleifend, tortor enim consequat orci, a sagittis diam magna eu ligula. Sed dapibus facilisis nulla at tincidunt. Nulla blandit feugiat purus, a pulvinar ante. Vestibulum mollis elit ut risus facilisis, mattis venenatis metus iaculis. Fusce sed cursus sem, nec ornare erat. </p>
        <br>
    </div>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;" onclick="window.location.href='/compositions/create?composer={{$defaultComposer}}'">Share Your Story</button>
                    </div>
                </div>
                <br>
                <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;" onclick="window.location.href='/{{$project->id}}/compositions'">Explore All Stories</button>
                    </div>
                </div>
                <br>
                <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-xs-1">
                    </div>
                    <div class="col-xs-11">
                        <button class="btn btn-warning" style="width:200px;">Create a Story of Stories</button>
                    </div>
                </div>
                <br>
                <p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p>
            </div>
        </div>
@stop

@section('footer_right')
    @if ($owner)
        <a href="/projects/{{$project->id}}"> Project Page </a>
    @endif
@stop