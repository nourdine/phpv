<?php

namespace phpv\validator\single\native;

/**
 * Check if a KeyValue does not match the comparison string. 
 */
class DiversityValidator extends ComparisonValidator {

   public function executeComparison($value, $comparison) {
      return $value !== $comparison;
   }
}