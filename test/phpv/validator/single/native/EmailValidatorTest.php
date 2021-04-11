<?php

use PHPUnit\Framework\TestCase;
use phpv\validator\Validator;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\EmailValidator;
use phpv\input\KeyValue;

class EmailValidatorTest extends TestCase {

   private $fv = null;

   public function setUp() : void {
      $this->fv = new KeyValueSetValidator();
   }

   public function tearDown() : void {
      unset($this->fv);
   }

   public function testStandalone() {
      $email = new EmailValidator(new KeyValue("mail", "laurent@codegist.org"), "invalid email");
      $this->assertTrue($email->getValidationOutput()->isValid());
   }

   public function testValidEmail() {
      $email = new EmailValidator(new KeyValue("mail", "laurent@codegist.org"), "invalid email");
      $this->fv->addValidator($email);
      $out = $this->fv->getValidationOutput();
      $this->assertTrue($out->isValid());
   }

   public function testInvalidEmail() {
      $email = new EmailValidator(new KeyValue("mail", "laurent@nonexistingdomainblablabla.org"), "invalid email", true);
      $this->fv->addValidator($email);
      $out = $this->fv->getValidationOutput();
      $errors = $out->getPackedErrors();
      $this->assertFalse($out->isValid());
      $this->assertEquals($errors["mail"], "invalid email");
   }
}