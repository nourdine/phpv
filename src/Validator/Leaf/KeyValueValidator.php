<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf;

use Phpv\Input\KeyValue;
use Phpv\Output\Error;
use Phpv\Output\PlaceHolderResolver;

abstract class KeyValueValidator extends LeafValidator
{
   protected $errorMessage;
   protected $kv = null;

   public function __construct(KeyValue $kv, string $errorMessage)
   {
      parent::__construct();
      $this->kv = $kv;
      $this->errorMessage = $errorMessage;
   }

   public function getCollectedValues(): array
   {
      return [
         $this->kv->getKey() => $this->kv->getValue()
      ];
   }

   /**
    * Call this method from the `validate` method of subclasses to register errors that will mark as invalid the validation parcel.
    */
   protected function registerError(string $errorMessage): void
   {
      $this->parcel->addError(
         new Error(
            $this->kv->getKey(),
            $this->kv->getValue(),
            PlaceHolderResolver::resolvePlaceHolder($errorMessage, $this->kv->getValue())));
   }
}
