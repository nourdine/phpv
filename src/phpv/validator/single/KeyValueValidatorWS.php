<?php

namespace phpv\validator\single;

use phpv\validator\single\KeyValueValidator;
use phpv\input\KeyValue;

/**
 * Spacialization of KeyValueValidator that allows acceptance of whitespace as an option.
 */
abstract class KeyValueValidatorWS extends KeyValueValidator {

   const ALLOW_WHITE_SPACE = true;

   public function __construct(KeyValue $kv, $allowWhiteSpace = false) {
      parent::__construct($kv);
      $this->whiteSpaceAllowed = $allowWhiteSpace;
   }
}