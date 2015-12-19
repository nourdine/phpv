<!DOCTYPE html>

<html lang="en">

   <head>

      <title>phpv example</title>
      <link rel="stylesheet" type="text/css" href="common.css" />

   </head>
   <body>

      <h1>phpv : form validation example</h1>

      <?php

      include_once '../../src/phpv/autoload.php';

      use phpv\input\KeyValue;
      use phpv\validator\set\KeyValueSetValidator;
      use phpv\validator\single\native\SizeRangeValidator;
      use phpv\validator\single\native\DiversityValidator;
      use phpv\validator\single\native\OneOfTheseValuesValidator;
      use phpv\validator\single\native\TautologicValidator;
      use phpv\validator\single\native\AlphaNumericalValidator;
      use phpv\validator\single\native\AtLeastOnePositiveValidator;
      use phpv\output\printer\HTMLErrorPrinter;

      $output = null;
      $valid = false;

      if (isset($_POST["submit"])) {

         $form = new KeyValueSetValidator(new HTMLErrorPrinter());
         $name = KeyValue::obtainFromHTTP("name");
         $password = KeyValue::obtainFromHTTP("password");
         $gender = KeyValue::obtainFromHTTP("gender");
         $browser = KeyValue::obtainFromHTTP("browser");
         $thoughts = KeyValue::obtainFromHTTP("thoughts");
         $football = KeyValue::obtainFromHTTP("football");
         $tennis = KeyValue::obtainFromHTTP("tennis");
         $swimming = KeyValue::obtainFromHTTP("swimming");

         $nameValidator = new SizeRangeValidator($name, "name `%value%` must be between 3 and 20", 3, 20);
         $nameValidator2 = new AlphaNumericalValidator($name, "name must be alphanumerical");
         $passwordValidator = new SizeRangeValidator($password, "password between 3 and 20", 3, 20);
         $genderValidator = new OneOfTheseValuesValidator($gender, "only 'male' or 'female' are permitted", array("m", "f"));
         $browserValidator = new DiversityValidator($browser, "choose your browser of choice", "-");
         $thoughtsValidator = new SizeRangeValidator($thoughts, "keep thoughts between 20 and 100 chars", 20, 100);
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

         $output = $form->getValidationOutput();
         $valid = $output->isValid();
      }

      ?>

      <?php if (!$valid) { ?>

         <?php

         if (isset($output)) {
            echo $output->displayErrors("p|errors-container|errors", "span|my-error");
         }

         ?>

         <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            
            <img id="logo" src="../../../icon.png" alt="" />

            <div class="col">
               <label>your name:</label>
               <input type="text" name="name" value="<?= KeyValueSetValidator::getCollectedValue('name') ?>"/>

               <label>your password:</label>
               <input type="password" name="password" />

               <label>your gender:</label>

               <?= KeyValueSetValidator::radioButton('gender', 'm', true); ?> male
               <?= KeyValueSetValidator::radioButton('gender', 'f'); ?> female
               <?= KeyValueSetValidator::radioButton('gender', 'non'); ?> non-permitted-value

               <label>your favourite browser:</label>

               <?=

               KeyValueSetValidator::select("browser", array(
                  "b1" => "firefox",
                  "b2" => "chrome",
                  "b3" => "ie",
                  "b4" => "opera",
                  "b5" => "safari"
               ))

               ?>

               <p>
                  <input type="submit" name="submit" value="submit" />
               </p>

            </div>

            <div class="col">
               
               <label>Tell me what you think:</label>
               <textarea name="thoughts" rows="5" cols="40"><?= KeyValueSetValidator::getCollectedValue('thoughts') ?></textarea>
               
               <label>What sports do you like?</label>
               <?= KeyValueSetValidator::checkbox('football', true); ?> football
               <?= KeyValueSetValidator::checkbox('tennis'); ?> tennis
               <?= KeyValueSetValidator::checkbox('swimming'); ?> swimming
            </div>

         </form>

      <?php } else { ?>

         <p>VERY GOOD! Form submitted successfully! :)</p>

      <? } ?>

   </body>

</html>
