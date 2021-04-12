<?php

namespace phpv\validator\single;

use phpv\validator\LeafValidator;
use phpv\input\KeyValue;
use phpv\output\Error;
use phpv\output\PlaceHolderResolver;

/**
 * Class executing the validation of a KeyValue object.
 */
abstract class KeyValueValidator extends LeafValidator {

   protected $kv = null;
   protected $whiteSpaceAllowed = false;

   /**
    * Use this method in KeyValueValidator::validate to regist errors that will then mark as unvalid the current validator itself.
    *
    * @param string $errorMessage The error message 
    */
   protected function registerError($errorMessage) {
      $errorMessageCompiled = PlaceHolderResolver::resolvePlaceHolder($errorMessage, $this->kv->getValue());
      $this->parcel->addRawError(new Error($this->kv->getKey(), $this->kv->getValue(), $errorMessageCompiled));
   }

   public function __construct(KeyValue $kv) {
      parent::__construct();
      $this->kv = $kv;
   }

   public function getCollectedValues() {
      return array(
         $this->kv->getKey() => $this->kv->getValue()
      );
   }
}