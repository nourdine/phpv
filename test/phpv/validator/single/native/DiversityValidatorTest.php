<?php

use PHPUnit\Framework\TestCase;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\DiversityValidator;
use phpv\input\KeyValue;

class DiversityValidatorTest extends TestCase {

   const ERR_MSG = "equal but should be different";
   const INPUT_NAME = "username";
   const TARGET_VALUE = "target";

   private $fv = null;

   public function setUp() : void {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() : void {
      unset($this->fv);
   }

   public function testMatchingValue() {
      $userNameValidator = new DiversityValidator(new KeyValue(self::INPUT_NAME, self::TARGET_VALUE), self::ERR_MSG, self::TARGET_VALUE);
      $this->fv->addValidator($userNameValidator);
      $out = $this->fv->getValidationOutput();
      $errorMessages = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errorMessages[self::INPUT_NAME], self::ERR_MSG);
   }

   public function testNotMatchingValue() {
      $userNameValidator = new DiversityValidator(new KeyValue(self::INPUT_NAME, "laurent"), self::ERR_MSG, self::TARGET_VALUE);
      $this->fv->addValidator($userNameValidator);
      $out = $this->fv->getValidationOutput();
      $errorMessages = $out->getPackedErrors();
      $this->assertTrue($out->isValid());
   }

   public function testEmptyValue() {
      $userNameValidator = new DiversityValidator(new KeyValue(self::INPUT_NAME, ""), self::ERR_MSG, self::TARGET_VALUE);
      $this->fv->addValidator($userNameValidator);
      $out = $this->fv->getValidationOutput();
      $errorMessages = $out->getPackedErrors();
      $this->assertTrue($out->isValid());
   }
}