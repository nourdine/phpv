<?php

use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\SizeRangeValidator;
use phpv\input\KeyValue;

class CompositionTest extends PHPUnit_Framework_TestCase {

   private $fv1 = null;
   private $fv2 = null;

   public function setUp() {
      $this->fv1 = new KeyValueSetValidator();
      $this->fv2 = new KeyValueSetValidator();
   }

   public function tearDown() {
      unset($this->fv1);
      unset($this->fv2);
   }

   public function testFormsComposition() {
      $userNameValidator = new SizeRangeValidator(new KeyValue("username", "fabssss"), 6, 20, "too short", "too long");
      $otherKeyValueSetValidator = new SizeRangeValidator(new KeyValue("_", "123"), 6, 20, "too short", "too long");
      $this->fv1->addValidator($userNameValidator);
      $this->fv2->addValidator($otherKeyValueSetValidator);
      $this->fv1->addValidator($this->fv2);      
      $out = $this->fv1->getValidationOutput();
      $this->assertFalse($out->isValid());
   }
}