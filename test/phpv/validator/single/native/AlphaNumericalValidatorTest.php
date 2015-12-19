<?php

use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\AlphaNumericalValidator;
use phpv\input\KeyValue;

class AlphaNumericalValidatorTest extends PHPUnit_Framework_TestCase {

   const NAAN_MESSAGE = "not a alphanumerical string";

   private $fv = null;

   public function setUp() {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() {
      unset($this->fv);
   }

   public function testConformingValue() {
      $strValidator = new AlphaNumericalValidator(new KeyValue("str", "abc123XYZ"), self::NAAN_MESSAGE);
      $this->fv->addValidator($strValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testConformingValue2() {
      $strValidator = new AlphaNumericalValidator(new KeyValue("str", "abc"), self::NAAN_MESSAGE);
      $this->fv->addValidator($strValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testConformingValue3() {
      $strValidator = new AlphaNumericalValidator(new KeyValue("str", "123"), self::NAAN_MESSAGE);
      $this->fv->addValidator($strValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testNONConformingValue() {
      $strValidator = new AlphaNumericalValidator(new KeyValue("str", "ab%cd"), self::NAAN_MESSAGE);
      $this->fv->addValidator($strValidator);
      $out = $this->fv->getValidationOutput();
      $errorMessages = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errorMessages["str"], self::NAAN_MESSAGE);
   }

   public function testNONConformingValue2() {
      $strValidator = new AlphaNumericalValidator(new KeyValue("str", "ab cd"), self::NAAN_MESSAGE);
      $this->fv->addValidator($strValidator);
      $out = $this->fv->getValidationOutput();
      $errors = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors["str"], self::NAAN_MESSAGE);
   }

   public function testNONConformingValueWS() {
      $strValidator = new AlphaNumericalValidator(new KeyValue("str", " 1a 2b 3c 4d "), self::NAAN_MESSAGE, AlphaNumericalValidator::ALLOW_WHITE_SPACE);
      $this->fv->addValidator($strValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }
}