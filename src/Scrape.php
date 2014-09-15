<?php
/*
 * Resque-compatible scrape listing task
 */
class Scrape {
  function perform() {
    $q  = DB::query('SELECT link, neighborhood FROM listings WHERE scraped != TRUE', PDO::FETCH_ASSOC);
    $ps = DB::prepare('UPDATE listings SET scraped=TRUE, street=:street, description=:description, lat=:lat, lng=:lng WHERE link=:link');
    /*
    Guzzle::sendAll(array_map(function ($listing) {
      return Guzzle::createRequest('GET', 'http://newyork.craigslist.org' . $listing['link']);
    }, iterator_to_array($q)), ['complete' => function ($event) use($ps) {
      try {        
        $body = $event->getResponse()->getBody();
      
        $crawler = new Crawler($body);
        $readability = new Readability($body);

        $street = $crawler->filter('.mapAndAttrs > .mapbox > div.mapaddress');
      
        $ps->execute([
          ':link' => parse_url($event->getRequest()->getUrl())['path'],
          ':lat'  => null,
          ':lng'  => null,
          ':street' => $street->count() ? $street->text() : null,
          ':description' => $readability->init() ? trim(strip_tags(tidy_parse_string($readability->getContent()->innerHTML, [], 'UTF8'))) : null    
        ]);
      } catch (Exception $e) {
        Logger::error($e->getMessage(), $ps->errorinfo());
      }
    }]);
    */
    foreach ($q as $listing) {
      try {
        $body = Guzzle::get('http://newyork.craigslist.org' . $listing['link'])->getBody();
        
        $crawler = new Crawler($body);
        $readability = new Readability($body);

        $street = $crawler->filter('.mapAndAttrs > .mapbox > div.mapaddress');
        
        $url  = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . ($street->count() ? $street->text() : $listing['neighborhood']);
        $json = json_decode(Guzzle::get($url)->getBody(), true);
        $loc  = isset($json['results'][0]) ? $json['results'][0]['geometry']['location'] : null;
        
        $ps->execute([
          ':link' => $listing['link'],
          ':lat'  => isset($loc['lat']) ? $loc['lat'] : null,
          ':lng'  => isset($loc['lng']) ? $loc['lng'] : null,
          ':street' => $street->count() ? $street->text() : null,
          ':description' => $readability->init() ? trim(strip_tags(tidy_parse_string($readability->getContent()->innerHTML, [], 'UTF8'))) : null    
        ]);
      } catch (Exception $e) {
        Logger::error($e->getMessage(), $ps->errorinfo());
      }
    }
    
  }
}
