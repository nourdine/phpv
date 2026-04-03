<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\KeyValueValidator;
use Phpv\Validator\Leaf\Native\Trait\ValidationTrait;

/**
 * Check if a KeyValue's length is between the provided min and max. 
 */
class SizeRangeValidator extends KeyValueValidator
{
   use ValidationTrait;

   private $min;
   private $max;

   public function __construct(KeyValue $kv, string $errorMessage, int $min, int $max)
   {
      $this->checkString($kv);
      parent::__construct($kv, $errorMessage);
      $this->min = $min;
      $this->max = $max;
   }

   protected function doValidate(): void
   {
      if (
         strlen($this->kv->getValue()) < $this->min ||
         strlen($this->kv->getValue()) > $this->max
      ) {
         $this->registerError($this->errorMessage);
      }
   }
}
