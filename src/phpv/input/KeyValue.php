<?php

namespace phpv\input;

use RuntimeException;

/**
 * Bean object representing a key/value entity.
 */
class KeyValue {

   const NOT_ON_CLI_EXC = "You are not running on the command line";
   const NOT_ON_HTTP_EXC = "You are not running on a webserver";

   const CLI_SERVER_ID = "cli";

   private $key = "";
   private $value = "";

   private static function isCLI() {
      return php_sapi_name() === self::CLI_SERVER_ID;
   }

   /**
    * Creates a KeyValue object retrieving the value from $argv. Use this method in CLI scripts only!
    * If the index doesn't exist in $argv array then a KeyValue containing an empty string is returned.
    * [HEY] $paramIndex is ZERO-indexed so the first parameter you pass the CLI has an index of 0 and so on.
    *
    * @param integer $paramIndex The index of the value to retrieve from the $argv array.
    *
    * @return KeyValue
    */
   public static function obtainFromCLI($paramIndex) {
      if (self::isCLI()) {
         $paramIndex = $paramIndex + 1;
         global $argv;
         $value = "";
         if (array_key_exists($paramIndex, $argv)) {
            $value = $argv[$paramIndex];
         }
         return new KeyValue($paramIndex, $value);
      }
      throw new RuntimeException(self::NOT_ON_CLI_EXC);
   }

   /**
    * Creates a KeyValue object retrieving the value form $_REQUEST.
    *
    * @param string $paramName The name of the value to retrieve and chuck in KeyValue object.
    *
    * @return KeyValue
    */
   public static function obtainFromHTTP($paramName) {
      if (!self::isCLI()) {
         $value = "";
         if (array_key_exists($paramName, $_REQUEST)) {
            $value = $_REQUEST[$paramName];
         }
         return new KeyValue($paramName, $value);
      }
      throw new RuntimeException(self::NOT_ON_HTTP_EXC);
   }

   public function __construct($key, $value) {
      $this->key = $key;
      $this->value = $value;
   }

   public function getKey() {
      return $this->key;
   }

   public function getValue() {
      return $this->value;
   }
}