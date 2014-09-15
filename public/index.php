<?php
require __DIR__ . '/../vendor/autoload.php';

$q    = DB::query('SELECT title, link, price, street, lat, lng FROM listings WHERE lat IS NOT NULL AND price < 2000', PDO::FETCH_ASSOC);
$json = json_encode(iterator_to_array($q));

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Listings</title>
    <style>
    html, body { height: 100%; margin: 0; }
    #map { width: 100%; height: 100%; }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script src="http://maps.google.com/maps/api/js"></script>
    <script>
    var listings = <?php echo $json ?>;

    var map = new google.maps.Map(document.getElementById('map'), {
      center: new google.maps.LatLng(40.7410815, -73.9853174),
      zoom: 12
    });
    
    new google.maps.TransitLayer().setMap(map);
    
    var infowindow = new google.maps.InfoWindow();
    
    for (var i = 0; i < listings.length; i++) {
      var marker = new google.maps.Marker({ position: new google.maps.LatLng(listings[i].lat, listings[i].lng), map: map });

      google.maps.event.addListener(marker, 'click', (function (marker, i) {
        return function () {
          infowindow.setContent(
            '$' + listings[i].price + ' <a target="_blank" href="http://newyork.craigslist.org'
            + listings[i].link + '">' + listings[i].title + '</a>'
          );
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
    </script>
  </body>
</html>
