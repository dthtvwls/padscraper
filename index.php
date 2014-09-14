<?php
require __DIR__ . '/vendor/autoload.php';

$db   = new PDO('mysql:host=localhost;dbname=craigslist', 'root');
$q    = $db->query('SELECT title, link, price, location FROM listings', PDO::FETCH_ASSOC);

$json = json_encode(iterator_to_array($q));

?>
<!DOCTYPE html>
<html> 
<head>
  <title>Google Maps Multiple Markers</title> 
  <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <style>
  html, body { height: 100%; margin: 0; }
  #map { width: 100%; height: 100%; }
  </style>
</head> 
<body>
  <div id="map"></div>

  <script>
    var locations = [
      { location: 'Empire State Building' },
      { location: 'Statue of Liberty' },
      { location: 'Times Square' },
      { location: 'Brooklyn Bridge' },
      { location: 'Empire State Building' },
    ];

    var map = new google.maps.Map(document.getElementById('map'), {
      center: new google.maps.LatLng(40.7410815, -73.9853174),
      zoom: 12
    });
    
    new google.maps.TransitLayer().setMap(map);
    
    var geocoder = new google.maps.Geocoder();
    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {
      /*marker = new google.maps.Marker({
        position: new google.maps.LatLng(40.7410815, -73.9853174),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function (marker, i) {
        return function () {
          infowindow.setContent(locations[i].location);
          infowindow.open(map, marker);
        }
      })(marker, i));*/
    }
    
    geocoder.geocode({ address: '333 Park Ave S, NY, NY' }, function (results, status) {
      if (status == 'OK') {
        var latlng = results[0].geometry.location;
        
        new google.maps.Marker({
          position: latlng,
          map: map
        });
      }
    });
  </script>
</body>
</html>
