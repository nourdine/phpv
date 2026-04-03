<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Validator\Leaf\Native\DiversityValidator;
use Phpv\Input\KeyValue;

class DiversityValidatorTest extends TestCase
{
   const ERR_MSG = "equal but should be different";
   const INPUT_NAME = "username";
   const COMPARISON_VALUE = "xxx";

   public function testMatchingValue()
   {
      $validator = new DiversityValidator(new KeyValue(self::INPUT_NAME, self::COMPARISON_VALUE), self::ERR_MSG, self::COMPARISON_VALUE);
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertFalse($output->isValid());
      $this->assertEquals($errorMessages[self::INPUT_NAME], self::ERR_MSG);
   }

   public function testNotMatchingValue()
   {
      $validator = new DiversityValidator(new KeyValue(self::INPUT_NAME, "Foo"), self::ERR_MSG, self::COMPARISON_VALUE);
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertTrue($output->isValid());
      $this->assertEquals(count($errorMessages), 0);
   }

   public function testEmptyValueIsValid()
   {
      $validator = new DiversityValidator(new KeyValue(self::INPUT_NAME, ""), self::ERR_MSG, self::COMPARISON_VALUE);
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertTrue($output->isValid());
      $this->assertEquals(count($errorMessages), 0);
   }
}
