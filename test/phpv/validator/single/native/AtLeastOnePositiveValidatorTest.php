<?php

use PHPUnit\Framework\TestCase;
use phpv\validator\set\KeyValueSetValidator;
use phpv\input\KeyValue;
use phpv\validator\single\native\AtLeastOnePositiveValidator;

class AtLeastOnePositiveValidatorTest extends TestCase {

   private $fv = null;

   public function setUp() : void {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() : void {
      unset($this->fv);
   }

   public function testStandaloneSuccess() {
      $validator = new AtLeastOnePositiveValidator(array(
         new KeyValue("tennis", "on"),
         new KeyValue("football", "off"),
         new KeyValue("swimming", "off")
      ), "error!", "on");
      $this->assertTrue($validator->getValidationOutput()->isValid());
   }

   public function testStandaloneFailure() {
      $validator = new AtLeastOnePositiveValidator(array(
         new KeyValue("tennis", "off"),
         new KeyValue("football", "off"),
         new KeyValue("swimming", "off")
      ), "error!", "on");
      $this->assertFalse($validator->getValidationOutput()->isValid());
   }
}