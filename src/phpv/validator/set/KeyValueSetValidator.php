<?php

namespace phpv\validator\set;

use phpv\validator\CompositeValidator;
use phpv\validator\set\html\HTMLHelper;
use phpv\output\printer\ErrorPrinter;
use phpv\output\printer\HTMLErrorPrinter;

/**
 * Allows the validation of a bunch of validators as a whole.
 */
class KeyValueSetValidator extends CompositeValidator {

   private static $instance = null;
   private static $helper = null;

   private static function getHTMLHelperInstance() {
      if (is_null(self::$helper)) {
         self::$helper = new HTMLHelper();
      }
      return self::$helper->setForm(self::$instance);
   }

   /**
    * Magic proxy to the wrapped HTMLHelper instance. 
    * It allows objects that are instances of this class to be treated as an HTMLHelper.
    */
   public static function __callStatic($method, $arguments) {
      return call_user_func_array(array(
         self::getHTMLHelperInstance(),
         $method), $arguments);
   }

   /**
    * @param ErrorPrinter $printer The printer to be used. If not specified the printer will default to an HTML printer. 
    */
   public function __construct(ErrorPrinter $printer = null) {
      if (is_null($printer)) {
         $printer = new HTMLErrorPrinter();
      }
      parent::__construct($printer);
      self::$instance = $this;
   }

   public function validate() {
      foreach ($this->validators as $v) {
         $out = $v->getValidationOutput();
         $valid = $out->isValid();
         if (!$valid) {
            foreach ($out->getRawErrors() as $error) {
               $this->parcel->addRawError($error);
            }
         }
      }
   }

   public function getCollectedValues() {
      $tmp = array();
      foreach ($this->validators as $validator) {
         $tmp = array_merge($tmp, $validator->getCollectedValues());
      }
      return $tmp;
   }
}