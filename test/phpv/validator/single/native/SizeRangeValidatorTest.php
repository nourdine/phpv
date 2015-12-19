<?php

use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\SizeRangeValidator;
use phpv\input\KeyValue;

class SizeRangeValidatorTest extends PHPUnit_Framework_TestCase {

   const INPUT_NAME = "username";
   const ERR_MSG = "error msg";
   
   private $fv = null;

   public function setUp() {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() {
      unset($this->fv);
   }

   public function testStandalone() {
      $userNameValidator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, "laurent"), self::ERR_MSG, 5, 20);
      $this->assertTrue($userNameValidator->getValidationOutput()->isValid());
   }

   public function testValidString() {
      $userNameValidator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, "laurent"), self::ERR_MSG, 5, 20);
      $this->fv->addValidator($userNameValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testTooShortAString() {
      $userNameValidator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, "fabs"), self::ERR_MSG, 5, 20);
      $this->fv->addValidator($userNameValidator);
      $out = $this->fv->getValidationOutput();
      $errors = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors[self::INPUT_NAME], self::ERR_MSG);
   }

   public function testTooLongAString() {
      $userNameValidator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, "tooooolongastring"), self::ERR_MSG, 5, 8);
      $this->fv->addValidator($userNameValidator);
      $out = $this->fv->getValidationOutput();
      $errors = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors[self::INPUT_NAME], self::ERR_MSG);
   }
}