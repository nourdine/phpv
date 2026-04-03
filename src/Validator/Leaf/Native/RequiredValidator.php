<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\KeyValueValidator;

/**
 * Check if a KeyValue is not empty.
 */
class RequiredValidator extends KeyValueValidator
{
   public function __construct(KeyValue $kv, string $errorMessage)
   {
      parent::__construct($kv, $errorMessage);
   }

   protected function doValidate(): void
   {
      if (empty($this->kv->getValue())) {
         $this->registerError($this->errorMessage);
      }
   }
}
