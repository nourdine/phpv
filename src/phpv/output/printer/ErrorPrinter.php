<?php

namespace phpv\output\printer;

abstract class ErrorPrinter {

   protected $errors = array();

   public abstract function displayErrors();

   public function setErrors(array $errors) {
      $this->errors = $errors;
   }
}