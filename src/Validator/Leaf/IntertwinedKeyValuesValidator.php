<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf;

use RuntimeException;
use Phpv\Input\KeyValue;
use Phpv\Output\Error;

/**
 * Validate the logical relationship between a group of keyValue objects.
 */
abstract class IntertwinedKeyValuesValidator extends LeafValidator
{
   const MERGED_NAME = "merged-name:";
   const MERGED_VALUE = "merged-value:";

   protected $keyValues;
   protected $errorMessage;

   public function __construct(array $keyValues, string $errorMessage)
   {
      parent::__construct();
      $this->keyValues = $keyValues;
      $this->errorMessage = $errorMessage;
      $this->checkType();
   }

   public function getCollectedValues(): array
   {
      return [
         $this->composeName($this->keyValues) => $this->composeValue($this->keyValues)
      ];
   }

   protected function registerError(string $errorMessage): void
   {
      $this->parcel->addError(new Error(
         $this->composeName($this->keyValues),
         $this->composeValue($this->keyValues),
         $errorMessage
      ));
   }

   private function checkType()
   {
      foreach ($this->keyValues as $kv) {
         if (!$kv instanceof KeyValue) {
            throw new RuntimeException("Only the KeyValue type is allowed");
         }
      }
   }

   private function composeName(array $kvs)
   {
      $i = 0;
      $n = self::MERGED_NAME;
      foreach ($kvs as $kv) {
         $n .= ($i === 0 ? "" : "|") . $kv->getKey();
         $i++;
      }
      return $n;
   }

   private function composeValue(array $kvs)
   {
      $i = 0;
      $v = self::MERGED_VALUE;
      foreach ($kvs as $kv) {
         $v .= ($i === 0 ? "" : "|") . $kv->getValue();
         $i++;
      }
      return $v;
   }
}
