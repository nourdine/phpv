<?php

use PHPUnit\Framework\TestCase;
use phpv\output\PlaceHolderResolver;
use phpv\validator\single\native\NumericalValidator;
use phpv\input\KeyValue;

class PlaceHolderResolverTest extends TestCase {

   const RV = "~";
   const MESSAGE_BEGIN = "message begin";
   const MESSAGE_END = "message end";
   private $str = null;

   public function setUp() : void {
      $this->str = self::MESSAGE_BEGIN . PlaceHolderResolver::PH . self::MESSAGE_END;
   }

   public function testDetect() {
      $yes = PlaceHolderResolver::containPlaceHolder($this->str);
      $this->assertTrue($yes);
   }

   public function testReplace() {
      $result = PlaceHolderResolver::resolvePlaceHolder($this->str, self::RV);
      $this->assertEquals($result, self::MESSAGE_BEGIN . self::RV . self::MESSAGE_END);
   }

   public function testReplacementInValidators() {
      $inputValue = "ABC";
      $inputName = "telephone";
      $message = self::MESSAGE_BEGIN . "%value%" . self::MESSAGE_END;
      $validator = new NumericalValidator(new KeyValue($inputName, $inputValue), $message);
      $errors = $validator->getValidationOutput()->getPackedErrors();
      $this->assertEquals($errors[$inputName], self::MESSAGE_BEGIN . $inputValue . self::MESSAGE_END);
   }
}