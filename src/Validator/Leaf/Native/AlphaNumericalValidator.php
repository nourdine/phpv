<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native;

use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\KeyValueValidator;
use Phpv\Validator\Leaf\Native\Trait\ValidationTrait;
use Phpv\Validator\Leaf\Native\Trait\WhiteSpaceTrait;

/**
 * Check if a KeyValue is a string only composed of numbers and letters (and optionally white space).
 */
class AlphaNumericalValidator extends KeyValueValidator
{
   use WhiteSpaceTrait, ValidationTrait;

   public function __construct(KeyValue $kv, string $errorMessage, bool $whiteSpaceAllowed = false)
   {
      $this->checkString($kv);
      parent::__construct($kv, $errorMessage);
      $whiteSpaceAllowed ? $this->allowWhiteSpace() : $this->forbidWhiteSpace();
   }

   protected function doValidate(): void
   {
      if (!$this->isCompliant($this->kv->getValue())) {
         $this->registerError($this->errorMessage);
      }
   }

   private function isCompliant(string $str): bool
   {
      $is = false;
      $pattern = $this->isWhiteSpaceAllowed() ? '/^[0-9a-z\s]{1,}$/i' : '/^[0-9a-z]{1,}$/i';
      $matches = preg_match($pattern, $str);
      if ($matches > 0) {
         $is = true;
      }
      return $is;
   }
}
