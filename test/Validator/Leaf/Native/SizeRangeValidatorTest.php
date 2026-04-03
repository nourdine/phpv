<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Validator\Leaf\Native\SizeRangeValidator;
use Phpv\Input\KeyValue;

class SizeRangeValidatorTest extends TestCase
{
   const INPUT_NAME = "username";
   const ERR_MSG = "error!";

   public function testOnlyStringsAreAccepted()
   {
      $this->expectException(InvalidArgumentException::class);
      new SizeRangeValidator(new KeyValue(self::INPUT_NAME, 123), self::ERR_MSG, 5, 20);
   }

   public function testValidString()
   {
      $validator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, "TheName"), self::ERR_MSG, 5, 20);
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testEmptyString()
   {
      $validator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, ""), self::ERR_MSG, 5, 20);
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors[self::INPUT_NAME], self::ERR_MSG);
   }

   public function testTooShortAString()
   {
      $validator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, "Foo"), self::ERR_MSG, 5, 20);
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors[self::INPUT_NAME], self::ERR_MSG);
   }

   public function testTooLongAString()
   {
      $validator = new SizeRangeValidator(new KeyValue(self::INPUT_NAME, "ToooooLong"), self::ERR_MSG, 5, 8);
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors[self::INPUT_NAME], self::ERR_MSG);
   }
}
