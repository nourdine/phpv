<?php

namespace phpv\validator\single\native;

use phpv\validator\single\KeyValueValidator;
use phpv\input\KeyValue;

/**
 * Check if a KeyValue is na well-formed email. It optionally make a DNS call to check the email exist. 
 */
class EmailValidator extends KeyValueValidator {

   private $remoteCheck = false;
   private $errorMessage = "";

   /**
    * implementation of isValidEmail by linuxjournal.
    * @link http://www.linuxjournal.com/article/9585?page=0,3
    * @return boolean
    */
   private function isValidEmail($email, $remoteCheck) {

      // check for all the non-printable codes in the standard ASCII set,
      // including null bytes and newlines, and exit immediately if any are found.
      if (preg_match("/[\\000-\\037]/", $email)) {
         return false;
      }
      $pattern = "/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD";
      if (!preg_match($pattern, $email)) {
         return false;
      }
      if ($remoteCheck === true) {
         // Validate the domain exists with a DNS check
         // if the checks cannot be made (soft fail over to true)
         list($user, $domain) = explode('@', $email);
         if (function_exists('checkdnsrr')) {
            if (!checkdnsrr($domain, "MX")) { // Linux: PHP 4.3.0 and higher & Windows: PHP 5.3.0 and higher
               return false;
            }
         } else if (function_exists("getmxrr")) {
            if (!getmxrr($domain, $mxhosts)) {
               return false;
            }
         }
      }
      return true;
   }

   /**
    * @param KeyValue $kv
    * @param string $errorMessage
    * @param boolean $remoteCheck 
    */
   public function __construct(KeyValue $kv, $errorMessage, $remoteCheck = false) {
      parent::__construct($kv);
      $this->remoteCheck = $remoteCheck;
      $this->errorMessage = $errorMessage;
   }

   public function validate() {
      $valid = $this->isValidEmail($this->kv->getValue(), $this->remoteCheck);
      if (!$valid) {
         $this->registerError($this->errorMessage);
      }
   }
}