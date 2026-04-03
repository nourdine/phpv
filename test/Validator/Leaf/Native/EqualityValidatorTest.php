<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Validator\Leaf\Native\EqualityValidator;
use Phpv\Input\KeyValue;

class EqualityValidatorTest extends TestCase
{
   const ERR_MSG = "different but should be equal";
   const INPUT_NAME = "username";
   const COMPARISON_VALUE = "xxx";

   public function testMatchingValue()
   {
      $validator = new EqualityValidator(new KeyValue(self::INPUT_NAME, self::COMPARISON_VALUE), self::ERR_MSG, self::COMPARISON_VALUE);
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertTrue($output->isValid());
      $this->assertEquals(count($errorMessages), 0);
   }

   public function testNotMatchingValue()
   {
      $validator = new EqualityValidator(new KeyValue(self::INPUT_NAME, "Foo"), self::ERR_MSG, self::COMPARISON_VALUE);
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertFalse($output->isValid());
      $this->assertEquals($errorMessages[self::INPUT_NAME], self::ERR_MSG);
   }

   public function testEmptyValue()
   {
      $validator = new EqualityValidator(new KeyValue(self::INPUT_NAME, ""), self::ERR_MSG, self::COMPARISON_VALUE);
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertFalse($output->isValid());
      $this->assertEquals($errorMessages[self::INPUT_NAME], self::ERR_MSG);
   }
}
