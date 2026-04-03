<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\Native\MatchAnyOf;

class MatchAnyOfTest extends TestCase
{
   public function testOneValid()
   {
      $validator = new MatchAnyOf([
         new KeyValue("tennis", "on"),
         new KeyValue("football", "off"),
         new KeyValue("swimming", "on")
      ], "error!", "on");
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testTwoValid()
   {
      $validator = new MatchAnyOf([
         new KeyValue("tennis", "on"),
         new KeyValue("football", "off"),
         new KeyValue("swimming", "on")
      ], "error!", "on");
      $this->assertTrue($validator->validate()->isValid());
   }

   public function testAllInvalid()
   {
      $validator = new MatchAnyOf([
         new KeyValue("tennis", "off"),
         new KeyValue("football", "off"),
         new KeyValue("swimming", "off")
      ], "error", "on");
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertTrue(count($errors) === 1);
      $this->assertEquals($errors["merged-name:tennis|football|swimming"], "error");
   }

   public function testEmptyArray()
   {
      $validator = new MatchAnyOf([], "error", "on");
      $out = $validator->validate();
      $errors = $out->getStackedErrorMessages();
      $this->assertFalse($out->isValid());
      $this->assertTrue(count($errors) === 1);
      $this->assertEquals($errors["merged-name:"], "error");
   }
}
