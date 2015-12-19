<?php

namespace phpv\validator;

use phpv\output\printer\ErrorPrinter;

/**
 * Implements the bare minimum for an object to be called 'a composite validator' 
 */
abstract class CompositeValidator extends Validator {

   protected $validators = array();

   public function __construct(ErrorPrinter $printer) {
      parent::__construct($printer);
   }

   /**
    * Returns the number of contained validators.
    * @return integer 
    */
   public function countValidators() {
      return count($this->validators);
   }
   
   /**
    * Add a validator if not already in the list.
    * @param Validator $validator The validator to be added.
    */
   public function addValidator(Validator $validator) {
      foreach ($this->validators as $v) {
         if ($v === $validator) {
           return;
         }
      }
      $this->validators[] = $validator;
   }

   /**
    * Remove a validator.
    * @param Validator $validator The validator to be removed.
    */
   public function removeValidator(Validator $validator) {
      foreach ($this->validators as $key => $v) {
         if ($v === $validator) {
            unset($this->validators[$key]); // note this ain't leave a gap! Does a good job hey!
         }
      }
   }
}