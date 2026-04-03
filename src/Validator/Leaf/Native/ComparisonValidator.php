<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\KeyValueValidator;

abstract class ComparisonValidator extends KeyValueValidator
{
   private $comparison;
   
   public function __construct(KeyValue $kv, $errorMessage, $comparison)
   {
      parent::__construct($kv, $errorMessage);
      $this->comparison = $comparison;
   }

   protected function doValidate(): void
   {
      if (!$this->isHappy($this->kv->getValue(), $this->comparison)) {
         $this->registerError($this->errorMessage);
      }
   }

   abstract protected function isHappy($value, $comparison): bool;
}
