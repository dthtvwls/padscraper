<?php
class DB {
  static $connection;
  static function __callStatic($name, $args) {
    if (!self::$connection) self::$connection = new PDO('mysql:host=localhost;dbname=craigslist', 'root');
    return call_user_func_array([self::$connection, $name], $args);
  }
}
