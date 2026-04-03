<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Output\Error;
use Phpv\Output\ValidationParcel;

class ValidationParcelTest extends TestCase
{
   private $parcel = null;

   public function setUp(): void
   {
      $this->parcel = new ValidationParcel();
   }

   public function tearDown(): void
   {
      unset($this->parcel);
   }

   public function testInstance()
   {
      $this->assertTrue($this->parcel->isValid());
      $this->assertEquals($this->parcel->numOfErrors(), 0);
   }

   public function testErrorAddition()
   {
      $inputName = "_";
      $inputValue = "123";
      $errMessage = "the field _ is wrong";
      $this->parcel->addError(new Error($inputName, $inputValue, $errMessage));
      $this->assertFalse($this->parcel->isValid());
      $errors = $this->parcel->getStackedErrorMessages();
      $this->assertEquals(count($errors), 1);
      $this->assertEquals($errors[$inputName], $errMessage);
   }

   public function testReset()
   {
      $inputName = "_";
      $inputValue = "123";
      $errMessage = "the field _ is wrong";
      $this->parcel->addError(new Error($inputName, $inputValue, $errMessage));
      $this->assertFalse($this->parcel->isValid());
      $errors = $this->parcel->getStackedErrorMessages();
      $this->assertEquals(count($errors), 1);
      $this->assertEquals($errors[$inputName], $errMessage);
      $this->parcel->reset();
      $errors = $this->parcel->getStackedErrorMessages();
      $this->assertEquals(count($errors), 0);
      $this->assertTrue($this->parcel->isValid());
   }

   public function testMultipleValidations()
   {
      /**
       * Use cases: 
       * 
       * 1. A field is wrong for multiple reasons: an array of error messages for each invalid field
       * 2. A field is wrong for one single reason: a single error message
       */
      $inputName = "_";
      $inputValue = "123";
      $inputValueBis = "456";
      $errMessage = "the field _ is wrong";
      $errMessageBis = "the field _ is wrong (BIS)";

      $this->parcel->addError(new Error($inputName, $inputValue, $errMessage));
      $this->parcel->addError(new Error($inputName, $inputValueBis, $errMessageBis));

      $this->assertFalse($this->parcel->isValid());
      
      $errors = $this->parcel->getStackedErrorMessages();
      
      $this->assertEquals(count($errors), 1);
      $this->assertTrue(is_array($errors[$inputName]));      
      $this->assertEquals(count($errors[$inputName]), 2);
      $this->assertEquals($errors[$inputName][0], $errMessage);
      $this->assertEquals($errors[$inputName][1], $errMessageBis);
   }

   public function testNumberOfErrors()
   {
      $this->parcel->addError(new Error("_", "123", "error 1"));
      $this->parcel->addError(new Error("_", "456", "error 2"));
      $this->assertEquals($this->parcel->numOfErrors(), 2);
   }
}
