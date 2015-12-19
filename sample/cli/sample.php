<?php

include_once '../../src/phpv/autoload.php';

use phpv\input\KeyValue;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\SizeRangeValidator;
use phpv\validator\single\native\AlphaNumericalValidator;
use phpv\output\printer\CLIErrorPrinter;

$form = new KeyValueSetValidator(new CLIErrorPrinter());
$name = KeyValue::obtainFromCLI(0);

$nameValidator = new SizeRangeValidator($name, "the name must contain between 3 and 20 chars [provided > '%value%']", 3, 20);
$nameValidator2 = new AlphaNumericalValidator($name, "the name must not contain weird symbols [provided > '%value%']");
$form->addValidator($nameValidator);
$form->addValidator($nameValidator2);

$output = $form->getValidationOutput();

if ($output->isValid()) {
   fwrite(STDOUT, "Name is valid" . PHP_EOL);
} else {
   fwrite(STDOUT, $output->displayErrors());
}