@extends('layouts.default')

@section('header')
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />
<style>
  #map { width:50%; height:75%; background-color: #ff0000;}
</style>

@stop

@section('content')
<h1>Testing Map</h1>

<div style="height:600px;">
<div id='map'/>

<script>
console.log(DemocracyApps.start[0]);

// var map = L.mapbox.map('map', 'ejaxon.ipga8le2')
//                  .setView([{{$start[0]}}, {{$start[1]}}], 13); // AVL coordinates

// The coordinate array is in DemocracyApps.coords
var map = L.mapbox.map('map', 'ejaxon.ipga8le2')
                  .setView([DemocracyApps.start[0], DemocracyApps.start[1]], 13); // AVL coordinates

</script>
</div>
@stop
