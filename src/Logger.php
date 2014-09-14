<?php
class Logger {
  static $logger;
  static function __callStatic($name, $args) {
    if (!self::$logger) {
      self::$logger = new Monolog\Logger('debug');
      self::$logger->pushHandler(new Monolog\Handler\StreamHandler(__DIR__ . '/../log/debug.log'));
    }
    return call_user_func_array([self::$logger, $name], $args);
  }
}
