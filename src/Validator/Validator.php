<?php

declare(strict_types=1);

namespace Phpv\Validator;

use Phpv\Output\ValidationParcel;
use Phpv\Output\ValidationParcelProxy;

abstract class Validator
{
   protected $parcel;

   public function __construct()
   {
      $this->parcel = new ValidationParcel();
   }

   abstract public function getCollectedValues(): array;

   abstract public function addValidator(Validator $validator): void;

   abstract public function removeValidator(Validator $validator): void;

   public final function validate(): ValidationParcelProxy
   {
      $this->parcel->reset();
      $this->doValidate();
      return new ValidationParcelProxy(clone $this->parcel);
   }

   /**
    * To be overriden in subclasses.
    * It should call the overridden method registerError in order to register any detected error.
    */
   abstract protected function doValidate(): void;

   /**
    * To be overriden in subclasses.
    */
   abstract protected function registerError(string $errorMessage): void;
}
