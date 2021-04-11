<?php

use PHPUnit\Framework\TestCase;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\SizeRangeValidator;
use phpv\validator\single\native\AlphaNumericalValidator;
use phpv\input\KeyValue;

class CombiningValidatorsTest extends TestCase {

   const SIZE_RANGE_ERROR_MSG = "size range error";
   const NAAN_ERROR_MSG = "this is not a alphanumerical string";

   const INPUT_NAME = "i-name";

   private $fv = null;

   public function setUp() : void {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() : void {
      unset($this->fv);
   }

   public function testConformingValue() {
      $item = new KeyValue(self::INPUT_NAME, "abc123XYZ");
      $sizeRangeValidator = new SizeRangeValidator($item, self::SIZE_RANGE_ERROR_MSG, 2, 10);
      $alphanumericValidator = new AlphaNumericalValidator($item, self::NAAN_ERROR_MSG);
      $this->fv->addValidator($sizeRangeValidator);
      $this->fv->addValidator($alphanumericValidator);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testNONConformingValue() {
      $item = new KeyValue(self::INPUT_NAME, "abc123XYZ");
      $sizeRangeValidator = new SizeRangeValidator($item, self::SIZE_RANGE_ERROR_MSG, 2, 6);
      $alphanumericValidator = new AlphaNumericalValidator($item, self::NAAN_ERROR_MSG);
      $this->fv->addValidator($sizeRangeValidator);
      $this->fv->addValidator($alphanumericValidator);
      $out = $this->fv->getValidationOutput();
      $errorMessages = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errorMessages[self::INPUT_NAME], self::SIZE_RANGE_ERROR_MSG);
   }

   public function testNONConformingValue2() {
      $item = new KeyValue(self::INPUT_NAME, "abc12***3XYZ");
      $sizeRangeValidator = new SizeRangeValidator($item, self::SIZE_RANGE_ERROR_MSG, 2, 6);
      $alphanumericValidator = new AlphaNumericalValidator($item, self::NAAN_ERROR_MSG);
      $this->fv->addValidator($sizeRangeValidator);
      $this->fv->addValidator($alphanumericValidator);
      $out = $this->fv->getValidationOutput();
      $errorMessages = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals(count($errorMessages[self::INPUT_NAME]), 2);
      $this->assertEquals($errorMessages[self::INPUT_NAME][0], self::SIZE_RANGE_ERROR_MSG);
      $this->assertEquals($errorMessages[self::INPUT_NAME][1], self::NAAN_ERROR_MSG);
   }
}