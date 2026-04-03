<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Output\Error;
use Phpv\Output\ValidationParcel;
use Phpv\Output\ValidationParcelProxy;

class ValidationParcelProxyTest extends TestCase
{
   private $parcel;
   private $proxy;

   public function setUp(): void
   {
      $this->parcel = new ValidationParcel();
      $this->proxy = new ValidationParcelProxy($this->parcel);
   }

   public function tearDown(): void
   {
      unset($this->parcel);
      unset($this->proxy);
   }

   public function testListOfErrors()
   {
      $this->parcel->addError(new Error("name", "Foo", "name is too short"));
      $this->parcel->addError(new Error("name", "Foo", "name is too dumb"));
      $errors = $this->proxy->getErrors();
      $this->assertEquals(count($errors), 2);
      $this->assertEquals($errors[0]->message, "name is too short");
      $this->assertEquals($errors[1]->message, "name is too dumb");
   }

   public function testListOfErrorsIsADifferentArrary()
   {
      $this->parcel->addError(new Error("name", "Foo", "name is too short"));
      $this->parcel->addError(new Error("name", "Foo", "name is too dumb"));
      $originalErrors = $this->parcel->getErrors();
      $errors = $this->proxy->getErrors();
      unset($errors[0]);
      $this->assertEquals(count($originalErrors), 2);
      $this->assertEquals(count($errors), 1);
   }
}
