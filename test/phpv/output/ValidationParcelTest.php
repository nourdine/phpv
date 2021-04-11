<?php

use PHPUnit\Framework\TestCase;
use phpv\output\Error;
use phpv\output\ValidationParcel;
use phpv\validator\single\native\EmailValidator;
use phpv\validator\set\KeyValueSetValidator;
use phpv\input\KeyValue;

class ValidationParcelTest extends TestCase {

   private $parcel = null;

   public function setUp() : void {
      $this->parcel = new ValidationParcel();
   }

   public function tearDown() : void {
      unset($this->parcel);
   }

   public function testInstance() {
      $this->assertTrue($this->parcel->isValid());
      $this->assertEquals($this->parcel->numOfErrors(), 0);
   }

   public function testErrorAddition() {
      $inputName = "_";
      $inputValue = "123";
      $errMessage = "the field _ is wrong";
      $this->parcel->addRawError(new Error($inputName, $inputValue, $errMessage));
      $this->assertFalse($this->parcel->isValid());
      $errors = $this->parcel->getPackedErrors();
      $this->assertEquals($errors[$inputName], $errMessage);
   }

   public function testMultipleValidations() {
      $inputName = "_";
      $inputValue = "123";
      $inputValueBis = "456";
      $errMessage = "the field _ is wrong";
      $errMessageBis = "the field _ is just wrong (BIS)";
      $this->parcel->addRawError(new Error($inputName, $inputValue, $errMessage));
      $this->parcel->addRawError(new Error($inputName, $inputValueBis, $errMessageBis));
      $this->assertFalse($this->parcel->isValid());
      $errors = $this->parcel->getPackedErrors();
      $this->assertTrue(is_array($errors[$inputName]));
      $this->assertEquals($errors[$inputName][0], $errMessage);
      $this->assertEquals($errors[$inputName][1], $errMessageBis);
   }

   public function testErrorNumbers() {
      $this->parcel->addRawError(new Error("", "", ""));
      $this->parcel->addRawError(new Error("", "", ""));
      $this->assertEquals($this->parcel->numOfErrors(), 2);
   }

   public function testErrorPrinterWithCompositeValidator() {
      $v = new KeyValueSetValidator();
      $out = $v->getValidationOutput();
      $pr = $out->getErrorPrinter();
      $this->assertFalse(is_null($pr));
   }

   public function testErrorPrinterWithLeafValidator() {
      $this->expectException(\RuntimeException::class);

      $emailV = new EmailValidator(new KeyValue("email", "jedi@nourdine.net"), "error");
      $out = $emailV->getValidationOutput();
      $pr = $out->getErrorPrinter();
   }
}