<?php

namespace phpv\output;

use RuntimeException;
use phpv\output\Error;
use phpv\output\printer\ErrorPrinter;

/**
 * Logical representation of the validation process carrying info about the non-validating bits of info and the related error messages.
 * @method string displayErrors() Magic method returning a string reporting the validation errors. This method proxies ErrorPrinter::displayErrors.
 */
class ValidationParcel {

   const EXC_MSG = "This parcel doesn't own an error printer!";

   private $errors = array();
   private $valid = true;
   private $printer = null;

   /**
    * @param ErrorPrinter $errorPrinter Instances of LeafValidator don't need a printer so $errorPrinter is made optional here. 
    */
   public function __construct(ErrorPrinter $errorPrinter = null) {
      $this->printer = $errorPrinter;
   }

   public function __call($method, $arguments) {
      if ($method === "displayErrors") {
         return call_user_func_array(array(
            $this->getErrorPrinter(),
            $method), $arguments);
      }
   }

   public function addRawError(Error $err) {
      $this->valid = false;
      $this->errors[] = $err;
   }

   public function getRawErrors() {
      return $this->errors;
   }

   /**
    * Whether or not the parcel contains all valid data.
    * @return type 
    */
   public function isValid() {
      return $this->valid;
   }

   /**
    * Return the number of errors.
    * @return integer 
    */
   public function numOfErrors() {
      return count($this->errors);
   }

   /**
    * Return a multidimensional associative array containing the error messages associated to each piece of information being validated.
    * Each validated piece of information can contain a single error message (string) or a whole array of them.
    * @return array
    */
   public function getPackedErrors() {
      $tmp = array();
      foreach ($this->errors as $err) {
         $fieldName = $err->fieldName;
         if (array_key_exists($fieldName, $tmp)) {
            if (is_array($tmp[$fieldName])) {
               $tmp[$fieldName][] = $err->message;
            } else {
               $tmp[$fieldName] = array(
                  $tmp[$fieldName],
                  $err->message
               );
            }
         } else {
            $tmp[$fieldName] = $err->message;
         }
      }
      return $tmp;
   }

   /**
    * Get an ErrorPrinter pre-loaded with the errors contained by the parcel.
    * You will prolly not use this method much as the internal ErrorPrinter can be proxied directly using ValidationParcel::_call  
    * @return ErrorPrinter
    */
   public function getErrorPrinter() {
      if (!is_null($this->printer)) {
         $this->printer->setErrors($this->getPackedErrors());
         return $this->printer;
      }
      throw new RuntimeException(self::EXC_MSG);
   }
}
