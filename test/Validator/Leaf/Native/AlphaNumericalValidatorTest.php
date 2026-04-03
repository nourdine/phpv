<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\Native\AlphaNumericalValidator;

class AlphaNumericalValidatorTest extends TestCase
{
   const ERR_MESSAGE = "ivalid";

   public function testOnlyStringsAreAccepted()
   {
      $this->expectException(InvalidArgumentException::class);
      new AlphaNumericalValidator(new KeyValue("input-1", 123), self::ERR_MESSAGE);
   }

   public function testValid1()
   {
      $validator = new AlphaNumericalValidator(new KeyValue("input-1", "abc123XYZ"), self::ERR_MESSAGE);
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testValid2()
   {
      $validator = new AlphaNumericalValidator(new KeyValue("input-1", "abc"), self::ERR_MESSAGE);
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testValid3()
   {
      $validator = new AlphaNumericalValidator(new KeyValue("input-1", "123"), self::ERR_MESSAGE);
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testInvalid1()
   {
      $validator = new AlphaNumericalValidator(new KeyValue("input-1", "123%"), self::ERR_MESSAGE);
      $output = $validator->validate();
      $this->assertFalse($output->isValid());
      $this->assertEquals($output->getStackedErrorMessages()["input-1"], self::ERR_MESSAGE);
   }

   public function testInvalid2()
   {
      $validator = new AlphaNumericalValidator(new KeyValue("input-1", "ab cd"), self::ERR_MESSAGE);
      $output = $validator->validate();
      $this->assertFalse($output->isValid());
      $this->assertEquals($output->getStackedErrorMessages()["input-1"], self::ERR_MESSAGE);
   }

   public function testValidWithWhiteSpace()
   {
      $validator = new AlphaNumericalValidator(new KeyValue("input-1", " 1a 2b 3c 4d "), self::ERR_MESSAGE, true);
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testInvalidWithWhiteSpace()
   {
      $validator = new AlphaNumericalValidator(new KeyValue("input-1", " 1a 2b & 3c 4d "), self::ERR_MESSAGE);
      $output = $validator->validate();
      $this->assertFalse($output->isValid());
      $this->assertEquals($output->getStackedErrorMessages()["input-1"], self::ERR_MESSAGE);
   }
}
