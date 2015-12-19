<?php

namespace phpv\validator\single\native;

use phpv\input\KeyValue;
use phpv\validator\single\KeyValueValidator;

abstract class ComparisonValidator extends KeyValueValidator {

   private $comparison = "";
   private $errorMessage = "";

   /**
    * @param KeyValue   $kv              The object representing the value to validate.
    * @param string     $errorMessage    The massage to be made available when the two strings don't pass the comparison test.
    * @param string     $comparison      The String to be used for comparison.
    */
   public function __construct(KeyValue $kv, $errorMessage, $comparison) {
      parent::__construct($kv);
      $this->errorMessage = $errorMessage;
      $this->comparison = $comparison;
   }

   public function validate() {
      if (!$this->executeComparison($this->kv->getValue(), $this->comparison)) {
         $this->registerError($this->errorMessage);
      }
   }

   abstract public function executeComparison($value, $comparison);
}