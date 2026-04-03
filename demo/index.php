<?php

include_once(__DIR__ . "/../vendor/autoload.php");

// ### Example 1 ###

use Phpv\Input\KeyValue;
use Phpv\Validator\Branch\BranchValidator;
use Phpv\Validator\Leaf\IntertwinedKeyValuesValidator;
use Phpv\Validator\Leaf\KeyValueValidator;
use Phpv\Validator\Leaf\Native\SizeRangeValidator;
use Phpv\Validator\Leaf\Native\AlphaNumericalValidator;
use Phpv\Validator\Leaf\Native\EmailValidator;

// All values need to be wrapped up in a `KeyValue` object
$username = new KeyValue("username", "user&]"); // this contains special characters
$password = new KeyValue("password", "shortpass"); // this is too short

$formValidator = new BranchValidator();

$formValidator->addValidator(new SizeRangeValidator($username, "The username must have a length between 8 and 16", 8, 16));
$formValidator->addValidator(new AlphaNumericalValidator($username, "The username can only contain alphanumerical characters"));
$formValidator->addValidator(new SizeRangeValidator($password, "The password must have a length between 12 and 30", 12, 30));

$output = $formValidator->validate();

if ($output->isValid()) {
   echo "The provided data are valid. Thanks!" . PHP_EOL;
} else {
   print_r($output->getStackedErrorMessages());
}

// ### Example 2 ###

$email = new KeyValue("email", "admin@phpv.");
$validator = new EmailValidator($email, "The provided email '%value%' is not valid");
print_r($validator->validate()->getStackedErrorMessages());

// ### Example 3 ###

class BadWordsValidator extends KeyValueValidator
{
   // list of bad words to reject
   private $badWords = [
      "fool",
      "stupid",
      "idiot",
      // etc.
   ];

   public function __construct(KeyValue $kv, string $errorMessage)
   {
      parent::__construct($kv, $errorMessage);
   }

   public function doValidate(): void
   {
      foreach ($this->badWords as $bw) {
         if (strpos($this->kv->getValue(), $bw)) {
            $this->registerError($this->errorMessage); // let's register the error!
            return;
         }
      }
   }
}

$v = new BadWordsValidator(new KeyValue("description", "Someone is a fool around here!"), "Please do not swear");
print_r($v->validate()->getStackedErrorMessages());

// ### Example 4 ###

class SpecialRequestValidator extends IntertwinedKeyValuesValidator
{
   public function __construct(array $keyValues, string $errorMessage)
   {
      parent::__construct($keyValues, $errorMessage);
   }

   public function doValidate(): void
   {
      $checkbox = $this->keyValues["checkbox"];
      $textarea = $this->keyValues["textarea"];
      if ($checkbox->getValue() === "on" && $textarea->getValue() === "") {
         $this->registerError($this->errorMessage);
      }
   }
}

$checkbox = new KeyValue("special-request-wanted", "on");
$textarea = new KeyValue("special-request-text", "");
$v = new SpecialRequestValidator(array(
   "checkbox" => $checkbox,
   "textarea" => $textarea
), "You must describe your special request");

print_r($v->validate()->getStackedErrorMessages());
