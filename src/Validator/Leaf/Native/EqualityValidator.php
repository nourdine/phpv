<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

/**
 * Check if a KeyValue matches the provided value.
 */
class EqualityValidator extends ComparisonValidator
{
   public function isHappy($value, $comparison): bool
   {
      return $value === $comparison;
   }
}
