<?php

namespace phpv\validator\single\native;

use phpv\input\KeyValue;
use phpv\validator\single\KeyValueValidator;

/**
 * Check if a KeyValue's length is between a specified min and a max. 
 */
class SizeRangeValidator extends KeyValueValidator {

   private $errorMessage = "";
   private $min = -1;
   private $max = -1;

   /**
    * @param KeyValue   $kv               The object representing the value to validate.
    * @param string     $errorMessage     Error message.
    * @param number     $min              Minimum length for the string carried by KeyValue.
    * @param number     $max              Maximum length for the string carried by KeyValue.
    */
   public function __construct(KeyValue $kv, $errorMessage, $min, $max) {
      parent::__construct($kv);
      $this->errorMessage = $errorMessage;
      $this->min = $min;
      $this->max = $max;
   }

   public function validate() {
      if (strlen($this->kv->getValue()) < $this->min ||
      strlen($this->kv->getValue()) > $this->max) {
         $this->registerError($this->errorMessage);
      }
   }
}