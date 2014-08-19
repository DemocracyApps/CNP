<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>CNP</title>
	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);

		body {
			margin:10px;
			font-family:'Lato', sans-serif;
			text-align:left;
			color: #555;
		}

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
	</style>
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
        <link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />
        <style>
        #map { position:absolute; top:50px; bottom:10px; left:100px; width:50%; height:50%}
        </style>
</head>
<body>

<h1>Look around!</h1>
<br/>
<div id='map' width='500' height='400'/>

<script>

var map = L.mapbox.map('map', 'ejaxon.ipga8le2')
                  .setView([35.58, -82.5558], 13); // AVL coordinates

      var gj = {
      type: 'FeatureCollection',
      features: [{
         type: 'Feature',
         geometry: {
             type: 'Point',
             coordinates: [ 35.58, -82.5558 ]
                        },
         properties: {
             title: 'Asheville, NC',
             'marker-color': '#9c89cc',
             'marker-size': 'medium',
             'marker-symbol': 'building'
            }

       }
      ]
              };


var featureLayer = L.mapbox.featureLayer(gj).addTo(map);
</script>

</body>
</html>
