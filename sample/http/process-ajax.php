<?php

include_once '../../../phpv/src/phpv/autoload.php';

use phpv\input\KeyValue;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\SizeRangeValidator;
use phpv\validator\single\native\DiversityValidator;
use phpv\validator\single\native\OneOfTheseValuesValidator;
use phpv\validator\single\native\TautologicValidator;
use phpv\validator\single\native\AlphaNumericalValidator;
use phpv\validator\single\native\AtLeastOnePositiveValidator;
use phpv\output\printer\JSONErrorPrinter;

$form = new KeyValueSetValidator(new JSONErrorPrinter());
$name = KeyValue::obtainFromHTTP("name");
$password = KeyValue::obtainFromHTTP("password");
$gender = KeyValue::obtainFromHTTP("gender");
$browser = KeyValue::obtainFromHTTP("browser");
$thoughts = KeyValue::obtainFromHTTP("thoughts");
$football = KeyValue::obtainFromHTTP("football");
$tennis = KeyValue::obtainFromHTTP("tennis");
$swimming = KeyValue::obtainFromHTTP("swimming");

$nameValidator = new SizeRangeValidator($name, "name `%value%` must be between 3 and 20 chars long", 3, 20);
$nameValidator2 = new AlphaNumericalValidator($name, "name must be alphanumerical");
$passwordValidator = new SizeRangeValidator($password, "password must be between 3 and 20 chars long", 3, 20);
$genderValidator = new OneOfTheseValuesValidator($gender, "only 'male' or 'female' are permitted", array("m", "f"));
$browserValidator = new DiversityValidator($browser, "choose your browser of choice", "-");
$thoughtsValidator = new SizeRangeValidator($thoughts, "thoughts must contain between 20 and 100 chars", 20, 100);
$footballValidator = new TautologicValidator($football);
$tennisValidator = new TautologicValidator($tennis);
$swimmingValidator = new TautologicValidator($swimming);
$atLeastOneSport = new AtLeastOnePositiveValidator(array(
   $football,
   $tennis,
   $swimming
), "choose at least 1 sport you lazy arse!", "on");

$form->addValidator($nameValidator);
$form->addValidator($nameValidator2);
$form->addValidator($passwordValidator);
$form->addValidator($genderValidator);
$form->addValidator($browserValidator);
$form->addValidator($thoughtsValidator);
$form->addValidator($footballValidator);
$form->addValidator($tennisValidator);
$form->addValidator($swimmingValidator);
$form->addValidator($atLeastOneSport);

$out = $form->getValidationOutput();

echo $out->displayErrors();