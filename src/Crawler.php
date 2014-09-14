<?php
class Crawler {
  var $crawler;
  function __construct($body) {
    $this->crawler = new Symfony\Component\DomCrawler\Crawler;
    $this->crawler->addContent($body);
  }
  function __call($name, $args) {
    return $this->crawler->$name($args[0]);
  }
}
