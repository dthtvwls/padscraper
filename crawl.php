#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

$db        = new PDO('mysql:host=localhost;dbname=craigslist', 'root');
$statement = $db->prepare("INSERT INTO listings (title, link, date, price, location, code) VALUES (:title, :link, :date, :price, :location, :code)");
$guzzle    = new GuzzleHttp\Client;

array_map(function ($url) use ($guzzle) {
  $crawler = new \Symfony\Component\DomCrawler\Crawler();
  $crawler->addContent($guzzle->get($url)->getBody());
  
  $code = substr($url, 30, 3); // SUPER brittle obvs
  
  $crawler->filter('.row > .txt')->each(function ($node) use ($code) {
    try {
      global $statement;    
      $a = $node->filter('.pl > a.hdrlnk');
      $statement->execute([
        ':title'    => $a->text(),
        ':link'     => $a->attr('href'),
        ':date'     => strftime('%Y-%m-%d', strtotime($node->filter('.pl > .date')->text())),
        ':price'    => (($n = $node->filter('.l2 > .price'))       && $n->count()) ? preg_replace('/\D/', '', $n->text()) : null,
        ':location' => (($n = $node->filter('.l2 > .pnr > small')) && $n->count()) ? $n->text() : null,
        ':code'     => $code
      ]);
    } catch (Exception $e) {
      echo $e->getMessage();
      print_r($statement->errorinfo());
    }
    
  });
}, [
  'http://newyork.craigslist.org/nfa/',
  'http://newyork.craigslist.org/roo/',
  'http://newyork.craigslist.org/sub/',
]);
