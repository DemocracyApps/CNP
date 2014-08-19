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

var map = L.mapbox.map('map', 'ejaxon.ipga8le2')
                  .setView([35.58, -82.5558], 13); // AVL coordinates

    // L.geoJson({
    //   type: 'FeatureCollection',
    //   features: [{
    //      type: 'Feature',
    //      properties: {
    //          title: 'Asheville, NC',
    //          marker-color: '#9c89cc',
    //          marker-size: 'medium',
    //          marker-symbol: 'building'
    //                     },
    //      geometry: {
    //          type: 'Point',
    //          coordinates: [ 35.58, -82.5558 ]
    //        }
    //    }
    //   ]
    //  }).addTo(map);


//var featureLayer = L.mapbox.featureLayer(gj).addTo(map);
</script>
</div>
@stop
