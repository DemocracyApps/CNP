@extends('layouts.list')

@section('title')
    {{$vista->name}}
@stop

@section('listContent')
<?php
    $getParams = null;
    if ($composer) $getParams = '?composer='.$composer.'&vista='.$vista->id;
?>
    <table class="table table-striped">
        @foreach($elements as $element)
            <tr>
                <td style="width:20%;"> {{ $element->getId() }} </td>
                <td style="width:80%;"> <a href="/elements/{{$element->id}}{{$getParams}}">{{ $element->getName()}} </a></td>
            </tr>
        @endforeach
    </table>
    {{$elements->appends(\Request::except('page'))->links()}}
@stop