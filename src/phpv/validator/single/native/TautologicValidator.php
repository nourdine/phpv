<?php

namespace phpv\validator\single\native;

use phpv\input\KeyValue;
use phpv\validator\single\KeyValueValidator;

/**
 * This class is used to keep track of values that are not stricktly meant to take part to the validation process. 
 * This allows the view code to retain the values previously inserted and display them in the correspondent HTML form item for usability purposes.
 */
class TautologicValidator extends KeyValueValidator {

   public function __construct(KeyValue $kv) {
      parent::__construct($kv);
   }

   public function validate() {
      // do nothing!
   }
}