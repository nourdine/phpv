<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\Native\RequiredValidator;

class RequiredValidatorTest extends TestCase
{
   const ERR_MESSAGE = "not valid";

   public function testValid1()
   {
      $validator = new RequiredValidator(new KeyValue("input-1", "abc"), self::ERR_MESSAGE);
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testValid2()
   {
      $validator = new RequiredValidator(new KeyValue("input-1", "   "), self::ERR_MESSAGE);
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testInvalid()
   {
      $validator = new RequiredValidator(new KeyValue("input-1", ""), self::ERR_MESSAGE);
      $this->assertFalse($validator->validate()->isValid());
   }
}
