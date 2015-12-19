<?php

use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\NumericalValidator;
use phpv\input\KeyValue;

class NumericalValidatorTest extends PHPUnit_Framework_TestCase {

   const NAN_MESSAGE = "not a number";

   private $fv = null;

   public function setUp() {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() {
      unset($this->fv);
   }

   public function testConformingValue() {
      $ageValidator = new NumericalValidator(new KeyValue("age", "33"), self::NAN_MESSAGE);
      $this->fv->addValidator($ageValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testNONConformingValue() {
      $ageValidator = new NumericalValidator(new KeyValue("age", "33ABC"), self::NAN_MESSAGE);
      $this->fv->addValidator($ageValidator);
      $out = $this->fv->getValidationOutput();
      $errors = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors["age"], self::NAN_MESSAGE);
   }

   public function testNONConformingValue2() {
      $ageValidator = new NumericalValidator(new KeyValue("age", "3^3!"), self::NAN_MESSAGE);
      $this->fv->addValidator($ageValidator);
      $out = $this->fv->getValidationOutput();
      $errors = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors["age"], self::NAN_MESSAGE);
   }

   public function testConformingValueWS() {
      $ageValidator = new NumericalValidator(new KeyValue("age", "3   3"), self::NAN_MESSAGE, NumericalValidator::ALLOW_WHITE_SPACE);
      $this->fv->addValidator($ageValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testConformingValueWS2() {
      $ageValidator = new NumericalValidator(new KeyValue("age", "33"), self::NAN_MESSAGE, NumericalValidator::ALLOW_WHITE_SPACE);
      $this->fv->addValidator($ageValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }
}