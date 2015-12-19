<?php

namespace phpv\validator\single\native;

/**
 * Check if a KeyValue matches the provided comparison value. 
 */
class EqualityValidator extends ComparisonValidator {

   public function executeComparison($value, $comparison) {
      return $value === $comparison;
   }
}