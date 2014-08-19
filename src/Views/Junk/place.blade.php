@extends('layouts.default')

@section('header')
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />
<style>
  #map { position:absolute; top:50px; bottom:10px; left:100px; width:50%; height:50%}
  .back {background-color: red;}
</style>

@stop

@section('content')
<h1>Look around!</h1>

<div>Hello</div>
<br/>
<!--
<div class='back' id='map' width='500' height='400'/>

<script>
var map = L.mapbox.map('map', 'ejaxon.ipga8le2')
             .setView([40, -74.50], 9);

// var map = L.mapbox.map('map', 'ejaxon.ipga8le2')
//                   .setView([35.58, -82.5558], 13); // AVL coordinates

    L.geoJson({
      type: 'FeatureCollection',
      features: [{
         type: 'Feature',
         properties: {
             title: 'Asheville, NC',
             marker-color: '#9c89cc',
             marker-size: 'medium',
             marker-symbol: 'building'
                        },
         geometry: {
             type: 'Point',
             coordinates: [ 35.58, -82.5558 ]
           }
       }
      ]
     }).addTo(map);


//var featureLayer = L.mapbox.featureLayer(gj).addTo(map);
</script>
-->
@stop
