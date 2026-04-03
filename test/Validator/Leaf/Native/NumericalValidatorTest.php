<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Validator\Leaf\Native\NumericalValidator;
use Phpv\Input\KeyValue;

class NumericalValidatorTest extends TestCase
{
   const ERR_MESSAGE = "not a number";

   public function testOnlyStringsAreAccepted()
   {
      $this->expectException(InvalidArgumentException::class);
      new NumericalValidator(new KeyValue("age", 123), self::ERR_MESSAGE);
   }

   public function testValid()
   {
      $validator = new NumericalValidator(new KeyValue("age", "33"), self::ERR_MESSAGE);
      $out = $validator->validate();
      $this->assertTrue($out->isValid());
   }

   public function testInvalid1()
   {
      $validator = new NumericalValidator(new KeyValue("age", "33ABC"), self::ERR_MESSAGE);
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors["age"], self::ERR_MESSAGE);
   }

   public function testInvalid2()
   {
      $validator = new NumericalValidator(new KeyValue("age", "3^3!"), self::ERR_MESSAGE);
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors["age"], self::ERR_MESSAGE);
   }

   public function testInvalid3()
   {
      $validator = new NumericalValidator(new KeyValue("age", "3 3"), self::ERR_MESSAGE);
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors["age"], self::ERR_MESSAGE);
   }

   public function testValidWithWhiteSpace()
   {
      $validator = new NumericalValidator(new KeyValue("age", "3 3"), self::ERR_MESSAGE, true);
      $out = $validator->validate();
      $this->assertTrue($out->isValid());
   }

   public function testInvalidWithWhiteSpace()
   {
      $validator = new NumericalValidator(new KeyValue("age", "3 A3"), self::ERR_MESSAGE);
      $out = $validator->validate();
      $this->assertFalse($out->isValid());
   }
}
