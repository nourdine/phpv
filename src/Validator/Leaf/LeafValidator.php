<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf;

use Phpv\Validator\Validator;
use RuntimeException;

abstract class LeafValidator extends Validator
{
   const EXC_MSG = "This is a leaf and does not contain other validators";

   public final function addValidator(Validator $v): void
   {
      throw new RuntimeException(self::EXC_MSG);
   }

   public final function removeValidator(Validator $v): void
   {
      throw new RuntimeException(self::EXC_MSG);
   }
}
