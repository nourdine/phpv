<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native\Trait;

trait WhiteSpaceTrait
{
   protected $allowed = false;

   public function allowWhiteSpace()
   {
      $this->allowed = true;
   }

   public function forbidWhiteSpace()
   {
      $this->allowed = false;
   }

   protected function isWhiteSpaceAllowed(): bool
   {
      return $this->allowed;
   }
}
