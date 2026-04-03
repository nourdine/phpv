<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Validator\Leaf\Native\InValidator;
use Phpv\Input\KeyValue;

class InValidatorTest extends TestCase
{
   public function testValid()
   {
      $v = new InValidator(new KeyValue("answer", "yes"), "answer must be either yes or no", ["yes", "no"]);
      $this->assertTrue($v->validate()->isValid());
   }

   public function testInvalid()
   {
      $v = new InValidator(new KeyValue("answer", "non_expected"), "answer must be either yes or no", ["yes", "no"]);
      $this->assertFalse($v->validate()->isValid());
   }
}
