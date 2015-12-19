<?php

namespace phpv\output\printer;

use phpv\output\printer\ErrorPrinter;
use phpv\validator\set\KeyValueSetValidator;

class JSONErrorPrinter extends ErrorPrinter {

   public function displayErrors() {
      return json_encode(array(
         "is-valid" => count($this->errors) === 0,
         "errors" => $this->errors
      ));
   }
}