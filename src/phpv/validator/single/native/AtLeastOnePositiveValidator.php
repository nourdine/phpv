<?php

namespace phpv\validator\single\native;

use phpv\validator\single\IntertwinedFormItemsValidator;

/**
 * Check if a KeyValue matches one of the provided values. 
 */
class AtLeastOnePositiveValidator extends IntertwinedFormItemsValidator {

   private $match = "";
   private $errorMessage = "";

   /**
    * @param array $keyValues
    * @param type $errorMessage
    * @param type $match 
    */
   public function __construct(array $keyValues, $errorMessage, $match) {
      parent::__construct($keyValues);
      $this->match = $match;
      $this->errorMessage = $errorMessage;
   }

   public function validate() {
      foreach ($this->keyValues as $kv) {
         if ($kv->getValue() === $this->match) {
            return;
         }
      }
      $this->registerError($this->errorMessage);
   }
}