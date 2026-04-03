<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Validator\Leaf\Native\EmailValidator;
use Phpv\Input\KeyValue;

class EmailValidatorTest extends TestCase
{
   public function testOnlyStringsAreAccepted()
   {
      $this->expectException(InvalidArgumentException::class);
      new EmailValidator(new KeyValue("email", 123), "invalid email");
   }

   public function testValidEmail()
   {
      $validator = new EmailValidator(new KeyValue("email", "aaa@bbb.ccc"), "invalid email");
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertTrue($output->isValid());
      $this->assertEquals(count($errorMessages), 0);
   }

   public function testInvalidEmail()
   {
      $validator = new EmailValidator(new KeyValue("email", "abc@adomain."), "invalid email");
      $output = $validator->validate();
      $errorMessages = $output->getStackedErrorMessages();
      $this->assertFalse($output->isValid());
      $this->assertEquals($errorMessages["email"], "invalid email");
   }
}
