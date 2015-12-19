<?php

namespace phpv\validator\single\native;

use phpv\input\KeyValue;
use phpv\validator\single\KeyValueValidatorWS;

/**
 * Check if a KeyValue is a string composed of numbers and letters (and optionally white spaces) only.
 */
class AlphaNumericalValidator extends KeyValueValidatorWS {

   private $errorMessage = "";
   
   private function isConformed($str, $allowWS) {
      $pattern = '/^[0-9a-z]{1,}$/i';
      if ($allowWS) {
         $pattern = '/^[0-9a-z\s]{1,}$/i';
      }
      $matches = preg_match($pattern, $str);
      if ($matches > 0) {
         return true;
      }
      return false;
   }

   /**
    * @param KeyValue $kv
    * @param string $errorMessage
    * @param boolean $allowWhiteSpace 
    */
   public function __construct(KeyValue $kv, $errorMessage, $allowWhiteSpace = false) {
      parent::__construct($kv, $allowWhiteSpace);
      $this->errorMessage = $errorMessage;
   }

   public function validate() {
      $valid = $this->isConformed($this->kv->getValue(), $this->whiteSpaceAllowed);
      if (!$valid) {
         $this->registerError($this->errorMessage);
      }
   }
}