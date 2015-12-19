<?php

namespace phpv\validator;

use phpv\output\ValidationParcel;
use phpv\output\printer\ErrorPrinter;

/**
 * Abstract root class with validation purposes.
 */
abstract class Validator {

   protected $parcel = null;

   /**
    * @param ErrorPrinter $printer The printer to be used to output error messages. 
    */
   public function __construct(ErrorPrinter $printer = null) {
      $this->parcel = new ValidationParcel($printer);
   }

   /**
    * Validate and return the result of validation.
    * @return ValidationParcel An object describing the validation state of the wrapped data.
    */
   public final function getValidationOutput() {
      $this->validate();
      return $this->parcel;
   }

   /**
    * Don't ever call this method directly but simply implement it!
    * Validator::getValidationOutput is responsible for calling this method.
    */
   abstract public function validate();

   /**
    * Returns an array containing all the values to be validated.
    * @return array
    */
   abstract public function getCollectedValues();
   
   abstract public function addValidator(Validator $v);

   abstract public function removeValidator(Validator $validatorToRemove);
}