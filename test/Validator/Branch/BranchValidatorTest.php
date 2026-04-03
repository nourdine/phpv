<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phpv\Input\KeyValue;
use Phpv\Validator\Branch\BranchValidator;
use Phpv\Validator\Leaf\Native\SizeRangeValidator;
use Phpv\Validator\Leaf\Native\AlphaNumericalValidator;
use Phpv\Validator\Leaf\Native\NumericalValidator;

class BranchValidatorTest extends TestCase
{
   private $validator1;
   private $validator2;

   public function setUp(): void
   {
      $this->validator1 = new BranchValidator();
      $this->validator2 = new BranchValidator();
   }

   public function tearDown(): void
   {
      unset($this->validator);
      unset($this->validator2);
   }

   public function testEmptyValidator()
   {
      $out = $this->validator1->validate();
      $this->assertTrue($out->isValid());
      $this->assertEquals($out->numOfErrors(), 0);
   }

   public function testValidatorAddition()
   {
      $userNameValidator = new SizeRangeValidator(new KeyValue("username", "Foo"), "error", 6, 20);
      $this->validator1->addValidator($userNameValidator);
      $out = $this->validator1->validate();
      $this->assertEquals($out->numOfErrors(), 1);
   }

   public function testValidatorMultipleAdditions()
   {
      $userNameValidator = new SizeRangeValidator(new KeyValue("username", "Foo"), "error", 6, 20);
      $this->validator1->addValidator($userNameValidator);
      $this->validator1->addValidator($userNameValidator);
      $out = $this->validator1->validate();
      $this->assertEquals($out->numOfErrors(), 1);
   }

   public function testValidatorRemoval()
   {
      $userNameValidator = new SizeRangeValidator(new KeyValue("username", "Foo"), "error", 6, 20);
      $this->validator1->addValidator($userNameValidator);
      $this->validator1->removeValidator($userNameValidator);
      $out = $this->validator1->validate();
      $this->assertEquals($out->numOfErrors(), 0);
   }

   public function testErrors()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "Foo"), "name wrong size", 6, 20));
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("surname", "White"), "surname wrong size", 2, 20));
      $this->validator1->addValidator(new AlphaNumericalValidator(new KeyValue("surname", "White"), "surname is not alphanumeric"));
      $out = $this->validator1->validate();
      $this->assertEquals($out->numOfErrors(),  1);
      $this->assertEquals($out->getStackedErrorMessages()["name"], "name wrong size");
   }

   public function testErrorsWithNestedBranch()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "Foo"), "name wrong size", 6, 20));
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("surname", "White"), "surname wrong size", 2, 20));
      $this->validator1->addValidator(new AlphaNumericalValidator(new KeyValue("surname", "White"), "surname is not alphanumeric"));

      $this->validator2->addValidator(new NumericalValidator(new KeyValue("age", "twelve"), "age must be a number"));
      $this->validator1->addValidator($this->validator2);

      $out = $this->validator1->validate();
      $this->assertEquals($out->numOfErrors(), 2);
      $this->assertEquals($out->getStackedErrorMessages()["name"], "name wrong size");
      $this->assertEquals($out->getStackedErrorMessages()["age"], "age must be a number");
   }

   public function testCollectedValues()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "Foo"), "name wrong size", 6, 20));
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("surname", "White"), "surname wrong size", 2, 20));
      $this->validator1->addValidator(new AlphaNumericalValidator(new KeyValue("surname", "White"), "surname is not alphanumeric"));
      $collectedValues = $this->validator1->getCollectedValues();
      $this->assertEquals(count($collectedValues), 2);
      $this->assertEquals($collectedValues["name"], "Foo");
      $this->assertEquals($collectedValues["surname"], "White");
   }

   public function testCollectedValuesOverriding()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "Foo"), "name wrong size", 6, 20));
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "Faa"), "name wrong size", 6, 20));
      $collectedValues = $this->validator1->getCollectedValues();
      $this->assertEquals(count($collectedValues), 1);
      $this->assertEquals($collectedValues["name"], "Faa");
   }

   public function testCollectedValuesWithNestedBranch()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "Foo"), "name wrong size", 6, 20));
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("surname", "White"), "surname wrong size", 2, 20));
      $this->validator1->addValidator(new AlphaNumericalValidator(new KeyValue("surname", "White"), "surname is not alphanumeric"));

      $this->validator2->addValidator(new NumericalValidator(new KeyValue("age", "12"), ""));
      $this->validator1->addValidator($this->validator2);

      $collected = $this->validator1->getCollectedValues();
      $this->assertEquals(count($collected), 3);
      $this->assertEquals($collected["name"], "Foo");
      $this->assertEquals($collected["surname"], "White");
      $this->assertEquals($collected["age"], "12");
   }

   public function testAddingValidatorsAfterGettingTheOutput()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "the name"), "name wrong size", 1, 20));
      $out1 = $this->validator1->validate();
      $this->assertTrue($out1->isValid());

      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("surname", "the surname"), "surname wrong size", 15, 20));
      $out2 = $this->validator1->validate();

      // Each validation parcel returned by `validate` is a snapshot at that point in time!
      $this->assertTrue($out1->isValid());
      $this->assertFalse($out2->isValid());
      $this->assertEquals($out1->numOfErrors(), 0);
      $this->assertEquals($out2->numOfErrors(), 1);
   }

   public function testValidationIdempotence()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("name", "the name"), "name wrong size", 1, 20));
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("surname", "the surname"), "surname wrong size", 15, 20));
      $out1 = $this->validator1->validate();
      $out1 = $this->validator1->validate();
      $out1 = $this->validator1->validate();
      $this->assertFalse($out1->isValid());
      $this->assertEquals($out1->numOfErrors(), 1);
   }

   public function testCompositionOfBranchesWhenOneBranchHasErrors()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("username", "the username"), "wrong size", 6, 20));
      $this->validator2->addValidator(new SizeRangeValidator(new KeyValue("password", "123"), "wrong size", 6, 20));
      $this->validator1->addValidator($this->validator2);
      $out = $this->validator1->validate();
      $this->assertFalse($out->isValid());
      $this->assertEquals($out->numOfErrors(), 1);
   }

   public function testCompositionOfBranchesWhenBothBranchesHaveErrors()
   {
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("username", "short"), "wrong size", 6, 20));
      $this->validator2->addValidator(new SizeRangeValidator(new KeyValue("password", "123"), "wrong size", 6, 20));
      $this->validator1->addValidator($this->validator2);
      $out = $this->validator1->validate();
      $this->assertFalse($out->isValid());
      $this->assertEquals($out->numOfErrors(), 2);
   }

   public function testSuperComposition()
   {
      $validator3 = new BranchValidator();
      $validator3->addValidator(new SizeRangeValidator(new KeyValue("address", "short address"), "wrong size", 20, 30)); // x
      $validator3->addValidator(new AlphaNumericalValidator(new KeyValue("address", "short address"), "only aplhanumeric")); // x
      $this->validator1->addValidator(new SizeRangeValidator(new KeyValue("username", "short"), "wrong size", 6, 20)); // x
      $this->validator2->addValidator(new SizeRangeValidator(new KeyValue("password", "123"), "wrong size", 6, 20)); // x 
      $this->validator2->addValidator($validator3);
      $this->validator1->addValidator($this->validator2);      
      $out = $this->validator1->validate();
      $this->assertFalse($out->isValid());
      $this->assertEquals($out->numOfErrors(), 4);
      $this->assertEquals(count($out->getStackedErrorMessages()), 3); // multiple messages for the same KeyValue are packed together
      $this->assertEquals(count($out->getStackedErrorMessages()["address"]), 2);
      $this->assertTrue(is_string($out->getStackedErrorMessages()["username"]));
      $this->assertTrue(is_string($out->getStackedErrorMessages()["password"]));
   }
}
