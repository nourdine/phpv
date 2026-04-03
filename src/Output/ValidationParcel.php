<?php

declare(strict_types=1);

namespace Phpv\Output;

/**
 * Logical representation of the validation process.
 * It carries information about the invalid bits and the related error messages.
 */
class ValidationParcel
{
   private $errors = [];

   public function addError(Error $error): void
   {
      $this->errors[] = $error;
   }

   public function reset(): void
   {
      $this->errors = [];
   }

   public function getErrors(): array
   {
      // Let's clone the array.
      // The contained errors are already immutable so I will not bother cloning them too.
      // The following code is not necessary (https://www.hackerone.com/blog/common-php-pitfalls-understanding-array-behavior-and-comparisons-python-and-javascript)
      // but I will write it anyway for clarity.
      // 
      $new = [];
      foreach ($this->errors as $err) {
         $new[] = $err;
      }
      return $new;
   }

   public function isValid(): bool
   {
      return $this->numOfErrors() === 0;
   }

   public function numOfErrors(): int
   {
      return count($this->errors);
   }

   /**
    * Return a multidimensional associative array containing the error messages associated to each piece of information being validated.
    * Each validated piece of information can contain a single error message (string) or an array of them.
    * 
    * For e.g:
    *  
    * [
    *    "field-1": "error message 1" 
    *    "field-2": [
    *       "error message 1",
    *       "error message 2" 
    *    ] 
    * ]
    */
   public function getStackedErrorMessages(): array
   {
      $tmp = [];
      foreach ($this->errors as $err) {
         $fieldName = $err->fieldName;
         if (array_key_exists($fieldName, $tmp)) {
            if (is_array($tmp[$fieldName])) {
               $tmp[$fieldName][] = $err->message;
            } else if (is_string($tmp[$fieldName])) {
               // I will put the already existing single string in an array along with the new one
               // the next time it will get handled by the main IF
               $tmp[$fieldName] = [
                  $tmp[$fieldName], // the already stored string
                  $err->message     // the new one
               ];
            }
         } else {
            $tmp[$fieldName] = $err->message;
         }
      }
      return $tmp;
   }
}
