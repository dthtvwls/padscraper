<?php
require __DIR__ . '/../vendor/autoload.php';

$q    = DB::query('SELECT title, link, price, street FROM listings WHERE street IS NOT NULL', PDO::FETCH_ASSOC);
$json = json_encode(iterator_to_array($q));

?>
<!DOCTYPE html>
<html>
<head>
  <title>Listings</title> 
  <script src="http://maps.google.com/maps/api/js"></script>
  <style>
  html, body { height: 100%; margin: 0; }
  #map { width: 100%; height: 100%; }
  </style>
</head>
<body>
  <div id="map"></div>

  <script>
    var listings = <?php echo $json ?>;

    var map = new google.maps.Map(document.getElementById('map'), {
      center: new google.maps.LatLng(40.7410815, -73.9853174),
      zoom: 12
    });
    
    new google.maps.TransitLayer().setMap(map);
    
    var geocoder = new google.maps.Geocoder();
    var infowindow = new google.maps.InfoWindow();

    
    
    function hitenter(value) {
      if (event.keyCode == 13) {
        geocoder.geocode({ address: value }, function (results, status) {
          if (status == 'OK') {
            console.log(results[0].geometry.location);
            new google.maps.Marker({ position: results[0].geometry.location, map: map });
          }
        });
      }
    }
    
    var offset = 0;
    //setInterval(function () {
      for (var i = 0; i < listings.length; i++) {
        (function (i) {
          if (!listings[i].location) return;
        
          geocoder.geocode({ address: listings[i].location }, function (results, status) {
            if (status == 'OK') {
              var marker = new google.maps.Marker({ position: results[0].geometry.location, map: map });

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
          });
        }(i));
      }
    //}, 1000); // limited to 10 queries/sec
  </script>
  <input onkeyup="hitenter(this.value)">
</body>
</html>
