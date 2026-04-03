<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

/**
 * Check if a KeyValue is different from the provided value.
 */
class DiversityValidator extends ComparisonValidator
{
   public function isHappy($value, $comparison): bool
   {
      return $value !== $comparison;
   }
}
