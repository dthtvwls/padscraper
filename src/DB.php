<?php
class DB {
  static $connection;
  static function __callStatic($name, $args) {
    if (!self::$connection) self::$connection = new PDO('mysql:host=localhost;dbname=craigslist', 'root');
    
    switch (count($args)) {
      case 2:
        return self::$connection->$name($args[0], $args[1]);
        break;
      case 1:
        return self::$connection->$name($args[0]);
        break;
      default:
        return self::$connection->$name();
    }
  }
}
