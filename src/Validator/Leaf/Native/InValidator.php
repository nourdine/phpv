<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

use Phpv\Validator\Leaf\KeyValueValidator;
use Phpv\Input\KeyValue;

/**
 * Check if the KeyValue matches one of the provided targets.
 */
class InValidator extends KeyValueValidator
{
   private $targets;

   public function __construct(KeyValue $kv, string $errorMessage, array $targets)
   {
      parent::__construct($kv, $errorMessage);
      $this->targets = $targets;
   }

   protected function doValidate(): void
   {
      if (array_search($this->kv->getValue(), $this->targets, true) === false) {
         $this->registerError($this->errorMessage);
      }
   }
}
