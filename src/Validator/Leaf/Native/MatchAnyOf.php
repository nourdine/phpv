<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

use Phpv\Validator\Leaf\IntertwinedKeyValuesValidator;

/**
 * Check if at least one of the KeyValues matches the provided target. 
 */
class MatchAnyOf extends IntertwinedKeyValuesValidator
{
   private $target;
   
   public function __construct(array $keyValues, string $errorMessage, mixed $target)
   {
      parent::__construct($keyValues, $errorMessage);
      $this->target = $target;
   }

   protected function doValidate(): void
   {
      foreach ($this->keyValues as $kv) {
         if ($kv->getValue() === $this->target) {
            return;
         }
      }
      $this->registerError($this->errorMessage);
   }
}
