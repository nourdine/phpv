<?php

use PHPUnit\Framework\TestCase;
use phpv\validator\Validator;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\OneOfTheseValuesValidator;
use phpv\input\KeyValue;

class OneOfTheseValuesValidatorTest extends TestCase {

   private $fv = null;

   public function setUp() : void {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() : void {
      unset($this->fv);
   }

   public function testStandalone() {
      $genderFailure = new OneOfTheseValuesValidator(new KeyValue("gender", "non_expected"), "gender must be either m or f", array("m", "f"));
      $this->assertFalse($genderFailure->getValidationOutput()->isValid());
      $genderSuccess = new OneOfTheseValuesValidator(new KeyValue("gender", "m"), "gender must be either m or f", array("m", "f"));
      $this->assertTrue($genderSuccess->getValidationOutput()->isValid());
   }
}