<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

use Phpv\Validator\Leaf\KeyValueValidator;
use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\Native\Trait\ValidationTrait;

/**
 * Check if a KeyValue is a well-formed email address.
 */
class EmailValidator extends KeyValueValidator
{
   use ValidationTrait;

   public function __construct(KeyValue $kv, string $errorMessage)
   {
      $this->checkString($kv);
      parent::__construct($kv, $errorMessage);
   }

   protected function doValidate(): void
   {
      if (!preg_match("/^(?:[a-zA-Z0-9_'^&\/+-])+(?:\.(?:[a-zA-Z0-9_'^&\/+-])+)*@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/", $this->kv->getValue())) {
         $this->registerError($this->errorMessage);
      }
   }
}
