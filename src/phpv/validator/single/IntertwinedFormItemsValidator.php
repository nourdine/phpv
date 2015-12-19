<?php

namespace phpv\validator\single;

use RuntimeException;
use phpv\validator\LeafValidator;
use phpv\input\KeyValue;
use phpv\output\Error;

/**
 * Execute the validation of the logical relationship between a group of keyValue objects.
 */
abstract class IntertwinedFormItemsValidator extends LeafValidator {

   const EXC_MSG = "Only KeyValue type is allowed here";
   const MERGED_NAME = "merged-name";
   const MERGED_VALUE = "merged-value";

   protected $keyValues = null;

   private function checkType(array $keyValues) {
      foreach ($keyValues as $kv) {
         if (!$kv instanceof KeyValue) {
            throw new RuntimeException(self::EXC_MSG);
         }
      }
   }

   private function composeName(array $kvs) {
      $n = self::MERGED_NAME;
      foreach ($kvs as $kv) {
         $n .= "|" . $kv->getKey();
      }
      return $n;
   }

   private function composeValue(array $kvs) {
      $v = self::MERGED_VALUE;
      foreach ($kvs as $kv) {
         $v .= "|" . $kv->getValue();
      }
      return $v;
   }

   protected function registerError($errorMessage) {
      $this->parcel->addRawError(new Error($this->composeName($this->keyValues), $this->composeValue($this->keyValues), $errorMessage));
   }

   public function __construct(array $keyValues) {
      parent::__construct();
      $this->checkType($keyValues);
      $this->keyValues = $keyValues;
   }

   public function getCollectedValues() {
      return array(
         $this->composeName($this->keyValues) => $this->composeValue($this->keyValues)
      );
   }
}