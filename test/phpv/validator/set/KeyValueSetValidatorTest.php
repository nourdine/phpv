<?php

use PHPUnit\Framework\TestCase;
use phpv\input\KeyValue;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\SizeRangeValidator;
use phpv\validator\single\native\AlphaNumericalValidator;
use phpv\validator\single\native\NumericalValidator;

class KeyValueSetValidatorTest extends TestCase {

   private $composite = null;

   public function setUp() : void {
      $this->composite = new KeyValueSetValidator();
   }

   public function tearDown() : void {
      unset($this->composite);
   }

   public function testEmptyKeyValueSetValidator() {
      $out = $this->composite->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testAddingValidator() {
      $userNameValidator = new SizeRangeValidator(new KeyValue("username", "fabs"), 6, 20, "error");
      $this->composite->addValidator($userNameValidator);
      $out = $this->composite->getValidationOutput();
      $this->assertFalse($out->isValid());
   }

   public function testCollectedValues() {
      $nameV = new SizeRangeValidator(new KeyValue("name", "fabs"), 6, 20, "");
      $surnameV = new SizeRangeValidator(new KeyValue("surname", "test"), 2, 20, "");
      $surnameV2 = new AlphaNumericalValidator(new KeyValue("surname", "test"), "");
      $this->composite->addValidator($nameV);
      $this->composite->addValidator($surnameV);
      $this->composite->addValidator($surnameV2);
      $collected = $this->composite->getCollectedValues();
      $this->assertEquals(count($collected), 2);
      $this->assertEquals($collected["name"], "fabs");
      $this->assertEquals($collected["surname"], "test");
   }

   public function testCollectedValuesWithCompoundedValidators() {
      $nameV = new SizeRangeValidator(new KeyValue("name", "fabs"), 6, 20, "", "");
      $surnameV = new SizeRangeValidator(new KeyValue("surname", "test"), 2, 20, "", "");
      $surnameV2 = new AlphaNumericalValidator(new KeyValue("surname", "test"), "");
      $composite2 = new KeyValueSetValidator();
      $composite2->addValidator(new NumericalValidator(new KeyValue("age", "12"), ""));
      $this->composite->addValidator($nameV);
      $this->composite->addValidator($surnameV);
      $this->composite->addValidator($surnameV2);
      $this->composite->addValidator($composite2);
      $collected = $this->composite->getCollectedValues();
      $this->assertEquals(count($collected), 3);
      $this->assertEquals($collected["name"], "fabs");
      $this->assertEquals($collected["surname"], "test");
      $this->assertEquals($collected["age"], "12");
   }

   public function testCannotAddTwiceTheSameValidator() {
      $nameV = new SizeRangeValidator(new KeyValue("name", "fabs"), 6, 20, "", "");
      $this->composite->addValidator($nameV);
      $this->composite->addValidator($nameV);
      $this->assertEquals(1, $this->composite->countValidators());
   }

   public function testAddingValidatorAfterGettingTheOutput() {
      $nameV = new SizeRangeValidator(new KeyValue("name", "fabs"), "", 1, 20);
      $surnameV = new SizeRangeValidator(new KeyValue("surname", "test"), "", 10, 20);
      $this->composite->addValidator($nameV);
      $out1 = $this->composite->getValidationOutput();
      $this->assertTrue($out1->isValid());
      $this->composite->addValidator($surnameV);
      $this->assertTrue($out1->isValid());
      $out2 = $this->composite->getValidationOutput();
      $this->assertFalse($out1->isValid());  
      $this->assertFalse($out2->isValid()); // $out1 e $out2 puntano allo stesso oggetto!     
   }
}