<?php

namespace phpv\validator\single\native;

use phpv\validator\single\KeyValueValidator;
use phpv\input\KeyValue;

/**
 * Check if a KeyValue is na well-formed email. It optionally make a DNS call to check the email exist. 
 */
class EmailValidator extends KeyValueValidator {

   private $errorMessage = "";

   /**
    * Implementation of internal isValidEmail logic taken from linuxjournal.
    *
    * @link http://www.linuxjournal.com/article/9585?page=0,3
    * @return boolean
    */
   private function isValidEmail($email) {

      // check for all the non-printable codes in the standard ASCII set,
      // including null bytes and newlines, and exit immediately if any are found.
      if (preg_match("/[\\000-\\037]/", $email)) {
         return false;
      }
      
      $pattern = "/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD";
      
      if (!preg_match($pattern, $email)) {
         return false;
      }

      return true;
   }

   /**
    * @param KeyValue $kv
    * @param string $errorMessage
    * @param boolean $remoteCheck 
    */
   public function __construct(KeyValue $kv, $errorMessage) {
      parent::__construct($kv);
      $this->errorMessage = $errorMessage;
   }

   public function validate() {
      $valid = $this->isValidEmail($this->kv->getValue());
      if (!$valid) {
         $this->registerError($this->errorMessage);
      }
   }
}