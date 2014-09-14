<?php
class Guzzle {
  static $client;
  static function __callStatic($name, $args) {
    if (!self::$client) self::$client = new GuzzleHttp\Client;
    return self::$client->$name($args[0]);
  }
}
