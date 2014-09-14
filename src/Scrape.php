<?php
class Scrape {
  function perform() {
    $q  = DB::query('SELECT id, link FROM listings WHERE scraped != TRUE', PDO::FETCH_ASSOC);
    $ps = DB::prepare('UPDATE listings SET scraped=TRUE, street=:street, description=:description, lat=:lat, lng=:lng WHERE id=:id');

    foreach ($q as $listing) {
      try {
        $body = Guzzle::get('http://newyork.craigslist.org' . $listing['link'])->getBody();
        
        $crawler = new Crawler($body);
        $readability = new Readability($body);

        $street = $crawler->filter('.mapAndAttrs > .mapbox > .mapaddress');
  
        $ps->execute([
          ':street' => $street->count() ? $street->text() : null,
          ':description' => $readability->init() ? strip_tags(tidy_parse_string($readability->getContent()->innerHTML, [], 'UTF8')) : null    
        ]);
      } catch (Exception $e) {
        echo $e->getMessage();
        print_r($statement->errorinfo());
      }
    }
  }
}