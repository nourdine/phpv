<?php

declare(strict_types=1);

namespace phpv\input;

/**
 * Entity representing a key/value pair
 */
class KeyValue
{
   private $key = "";
   private $value = "";

   public function __construct(string $key, mixed $value)
   {
      $this->key = $key;
      $this->value = $value;
   }

   public function getKey()
   {
      return $this->key;
   }

   public function getValue()
   {
      return $this->value;
   }
}
