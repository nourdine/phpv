<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Output\PlaceHolderResolver;
use Phpv\Input\KeyValue;
use Phpv\Validator\Leaf\Native\NumericalValidator;

class PlaceHolderResolverTest extends TestCase
{
   const VALUE = "~";
   const MESSAGE_BEGIN = "message begin";
   const MESSAGE_END = "message end";

   private $str;

   public function setUp(): void
   {
      $this->str = self::MESSAGE_BEGIN . PlaceHolderResolver::PH . self::MESSAGE_END;
   }

   public function testDetection()
   {
      $yes = PlaceHolderResolver::containsPlaceHolder($this->str);
      $this->assertTrue($yes);
   }

   public function testReplacement()
   {
      $result = PlaceHolderResolver::resolvePlaceHolder($this->str, self::VALUE);
      $this->assertEquals($result, self::MESSAGE_BEGIN . self::VALUE . self::MESSAGE_END);
   }

   public function testReplacementInQuotes1()
   {
      $result = PlaceHolderResolver::resolvePlaceHolder("hello '%value%' how are you?", "Foo");
      $this->assertEquals($result, "hello 'Foo' how are you?");
   }

   public function testReplacementInQuotes2()
   {
      $result = PlaceHolderResolver::resolvePlaceHolder("this is a wrong value: '%value%'", "Foo");
      $this->assertEquals($result, "this is a wrong value: 'Foo'");
   }

   public function testReplacementInQuotes3()
   {
      $result = PlaceHolderResolver::resolvePlaceHolder("'%value%' is a wrong value", "Foo");
      $this->assertEquals($result, "'Foo' is a wrong value");
   }

   public function testIntegrationWithValidator()
   {
      $inputValue = "ABC";
      $inputName = "telephone";
      $message = self::MESSAGE_BEGIN . "%value%" . self::MESSAGE_END;
      $validator = new NumericalValidator(new KeyValue($inputName, $inputValue), $message);
      $errors = $validator->validate()->getStackedErrorMessages();
      $this->assertEquals($errors[$inputName], self::MESSAGE_BEGIN . $inputValue . self::MESSAGE_END);
   }
}
