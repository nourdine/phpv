<?php

namespace phpv\output\printer;

use phpv\output\printer\ErrorPrinter;

class CLIErrorPrinter extends ErrorPrinter {

   const LINE_SYM = "# ";

   public function displayErrors() {
      $str = "";
      foreach ($this->errors as $error) {
         if (is_array($error)) {
            foreach ($error as $errorMsg) {
               $str .= self::LINE_SYM . $errorMsg . PHP_EOL;
            }
         } else {
            $str .= self::LINE_SYM . $error . PHP_EOL;
         }
      }
      return $str;
   }
}