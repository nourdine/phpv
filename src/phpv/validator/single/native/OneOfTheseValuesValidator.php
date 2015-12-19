<?php

namespace phpv\validator\single\native;

use phpv\validator\single\KeyValueValidator;
use phpv\input\KeyValue;

/**
 * Check if the KeyValue matches one of the provided allowed values.
 */
class OneOfTheseValuesValidator extends KeyValueValidator {

   private $errorMessage = "";
   private $values = null;

   /**
    * @param KeyValue  $kv              The object representing the value to validate.
    * @param string    $errorMessage    The error message.
    * @param array     $matches         An array containing the allowed values.
    */
   public function __construct(KeyValue $kv, $errorMessage, array $matches) {
      parent::__construct($kv);
      $this->errorMessage = $errorMessage;
      $this->values = $matches;
   }

   public function validate() {
      $valid = array_search($this->kv->getValue(), $this->values);
      if ($valid === false) {
         $this->registerError($this->errorMessage);
      }
   }
}