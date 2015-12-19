<?php

namespace phpv\validator;

use RuntimeException;

/**
 * A leaf to  be added to a composite validator.
 */
abstract class LeafValidator extends Validator {

   const EXC_MSG = "This is a leaf and cannot contain other validators";

   public function __construct() {
      parent::__construct(null); // A leaf does not require an ErrorPrinter.
   }

   // A leaf cannot contain other leafs.
   public final function addValidator(Validator $v) {
      throw new RuntimeException(self::EXC_MSG);
   }

   // A leaf cannot contain other leafs.
   public final function removeValidator(Validator $validatorToRemove) {
      throw new RuntimeException(self::EXC_MSG);
   }
}