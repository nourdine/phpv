<?php

declare(strict_types=1);

namespace Phpv\Validator\Branch;

use Phpv\Validator\Validator;

/**
 * This is a composite composite validator whose purpose is that of collecting leaves and validate them.
 */
class BranchValidator extends Validator
{
   protected $validators = [];

   public function countValidators(): int
   {
      return count($this->validators);
   }

   public function addValidator(Validator $validator): void
   {
      foreach ($this->validators as $v) {
         if ($v === $validator) {
            return;
         }
      }
      $this->validators[] = $validator;
   }

   public function removeValidator(Validator $validator): void
   {
      foreach ($this->validators as $key => $v) {
         if ($v === $validator) {
            unset($this->validators[$key]);
         }
      }
   }

   public function getCollectedValues(): array
   {
      $tmp = [];
      foreach ($this->validators as $validator) {
         $tmp = array_merge($tmp, $validator->getCollectedValues());
      }
      return $tmp;
   }

   protected function doValidate(): void
   {
      foreach ($this->validators as $v) {
         $out = $v->validate();
         $valid = $out->isValid();
         if (!$valid) {
            foreach ($out->getErrors() as $error) {
               $this->parcel->addError($error);
            }
         }
      }
   }

   // does nothing!
   protected function registerError(string $errorMessage): void {}
}
