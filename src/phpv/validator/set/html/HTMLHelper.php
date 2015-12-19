<?php

namespace phpv\validator\set\html;

use RuntimeException;
use phpv\validator\set\KeyValueSetValidator;

class HTMLHelper {

   const WRONG_TYPE_EXCEPTION_MSG = "Only type KeyValueSetValidator (or null) is allowed";

   private $form = null;

   private function isTypeAllowed($fv) {
      if ($fv instanceof KeyValueSetValidator || is_null($fv)) {
         return true;
      }
      return false;
   }

   private function isRadioButtonChecked($name, $value, $forceChecked) {
      if (!is_null($this->form)) {
         $vals = $this->form->getCollectedValues();
         if ($this->getCollectedValue($name) === $value) {
            return "checked='checked'";
         }
      } else {
         if ($forceChecked === true) {
            return "checked='checked'";
         }
      }
      return "";
   }

   private function isCheckboxChecked($name, $forceChecked) {
      if (!is_null($this->form)) {
         $vals = $this->form->getCollectedValues();
         if ($this->getCollectedValue($name) === "on") {
            return "checked='checked'";
         }
      } else {
         if ($forceChecked === true) {
            return "checked='checked'";
         }
      }
      return "";
   }

   public function __construct() {
      
   }
   
   /**
    * Set the form to which this helper is accociated.
    * @param KeyValueSetValidator|null $fv The form to monitor.
    * @return HTMLHelper
    */
   public function setForm($fv) {
      if (!$this->isTypeAllowed($fv)) {
         throw new RuntimeException(WRONG_TYPE_EXCEPTION_MSG);
      }
      $this->form = $fv;
      return $this;
   }

   /**
    * Retrieve the last value inserted for a certain input element. 
    * @param string $name The name of the HTML input to look for.
    * @return string The value of the input.
    */
   public function getCollectedValue($name) {
      if (!is_null($this->form)) {
         $vals = $this->form->getCollectedValues();
         if (array_key_exists($name, $vals)) {
            return $vals[$name];
         }
      }
      return "";
   }

   /**
    * Generate the code of an HTML radio button.
    * @param string $name The name of the generated radio button (this value will be used for the id as well).
    * @param string $value The value parameter of the generated radio button.
    * @param boolean $forceChecked Pass in true to generate the HTML of a checked radio button.
    * @return string The HTML code.
    */
   public function radioButton($name, $value, $forceChecked = false) {
      $checkedStr = $this->isRadioButtonChecked($name, $value, $forceChecked);
      return "<input type='radio' id='" . $name . "' name='" . $name . "' value='" . $value . "' " . $checkedStr . " />";
   }

   /**
    * Generate the code of an HTML checkbox.
    * @param string $name The name of the generated checkbox (this value will be used for the id as well).
    * @param boolean $forceChecked Pass in true to generate the HTML of a checked checkbox.
    * @return string The HTML code.
    */
   public function checkbox($name, $forceChecked = false) {
      $checkedStr = $this->isCheckboxChecked($name, $forceChecked);
      return "<input type='checkbox' id='" . $name . "' name='" . $name . "' " . $checkedStr . " />";
   }

   /**
    * Generate the code of an HTML select.
    * @param string $name The name of the generated select (this value will be used for the id as well). 
    * @param array $values An associative array containing data for the option tags generation.
    * @return string The HTML code.
    */
   public function select($name, array $values) {
      $html = "<select name='" . $name . "' id='" . $name . "'>";
      $html .= "<option value='-'>-</option>";
      $selectedItem = $this->getCollectedValue($name);
      foreach ($values as $key => $value) {
         if ($selectedItem === $key) {
            $html .= "<option value='" . $key . "' selected='selected'>" . $value . "</option>";
         } else {
            $html .= "<option value='" . $key . "'>" . $value . "</option>";
         }
      }
      return $html . "</select>";
   }
}