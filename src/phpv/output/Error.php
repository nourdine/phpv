<?php

namespace phpv\output;

class Error {

   public $fieldName = "";
   public $fieldValue = "";
   public $message = "";

   public function __construct($erroneousFieldName, $erroneousFieldValue, $errorMessage) {
      $this->fieldName = $erroneousFieldName;
      $this->fieldValue = $erroneousFieldValue;
      $this->message = $errorMessage;
   }
}