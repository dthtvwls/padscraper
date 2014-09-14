<?php
class Crawl {
  function perform() {
    $ps = DB::prepare('INSERT INTO listings SET scraped=FALSE, code=:code, title=:title, link=:link, date=:date, price=:price, neighborhood=:neighborhood');

    array_map(function ($url) use ($ps) {
  
      $code = substr($url, 30, 3); // SUPER brittle obvs
  
      $crawler = new Crawler(Guzzle::get($url)->getBody());
  
      $crawler->filter('.row > .txt')->each(function ($node) use ($ps, $code) {
        try {
          $a = $node->filter('.pl > a.hdrlnk');
          $ps->execute([
            ':code'  => $code,
            ':title' => $a->text(),
            ':link'  => $a->attr('href'),
            ':date'  => strftime('%Y-%m-%d', strtotime($node->filter('.pl > .date')->text())),
            ':price' => (($n = $node->filter('.l2 > .price')) && $n->count()) ? preg_replace('/\D/', '', $n->text()) : null,
            ':neighborhood' => (($n = $node->filter('.l2 > .pnr > small')) && $n->count()) ? $n->text() : null
          ]);
        } catch (Exception $e) {
          Logger::error($e->getMessage(), $ps->errorinfo());
        }
      });
    }, [
      'http://newyork.craigslist.org/nfa/',
      'http://newyork.craigslist.org/roo/',
      'http://newyork.craigslist.org/sub/'
    ]);
  }
}
