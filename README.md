PHPV
====

### 1. Intro

**phpv** is a data validation framework for php. The framework can be used in a wide range of scenarios and **NOT** necessarilly for data form validation over the internet (although that will be the purpose for which you are likely to use it the most). _phpv_ has been conceived with the [http://en.wikipedia.org/wiki/Composite_pattern composite pattern] in mind and thanks to this approach the validation of a single piece of data or the validation of a group of values (a form) can be carried out uniformely and taking exactly the same actions on the objects involved (more on this in paragraph 4 and 5).

### 2. Class loader

Once you have added phpv to your `composer.json` and run `composer install` you can then load your dependencies using the following:

```php
include_once 'vendor/autoload.php';
```

You probably knew this already but I thought to specify it anyway ;)

### 3. Validator

The interface `phpv\validator\Validator` is the very core of the whole library. As the name suggests this interface provide the capability of validating data. There are various implementations of this interface in the library that will help you carrying out your validation routines. At the same time you will be able to add your own validators and mix them up with the native ones _phpv_ is shipped with (see paragraph 7).

### 4. Branches and leafs

The composite patterns states that "the whole" can be treathed exactly like "the parts" composing it. A branch can contain leafs and any operation carried out on the branch will be reflected on the leafs as well. The same can be said about the validators _phpv_ is composed of. There are validators that can contain other validators (`phpv\validator\CompositeValidator`) as opposed to lonely validators (`phpv\validator\LeafValidator`) that cannot contain children validators. Validating a bunch of pieces of data is made easy by validating a `phpv\validator\CompositeValidator` containing all the single validators representing in turn the single pieces of information to be validated.

### 5. Simple validation example

Let's say we want to validate a signup form containing a username and a password. We start by creating a branch that will contain the single validators. _phpv_ offer a single concrete implementation of `phpv\validator\CompositeValidator`: `phpv\validator\set\KeyValueSetValidator`. I can't see the reason why one might want to have a different `phpv\validator\CompositeValidator` implementation but in case you feel such an impellent need, you go ahead and do some extension business yourself ;) Here's a snippet using some native _phpv_ classes:

```php
use phpv\input\KeyValue;
use phpv\validator\set\KeyValueSetValidator;
use phpv\validator\single\native\SizeRangeValidator;
use phpv\validator\single\native\AlphaNumericalValidator;

$formValidator = new KeyValueSetValidator();
```

Now we need to get the values from the http request for our username and password:

```php
$username = KeyValue::obtainFromHTTP("username");
$password = KeyValue::obtainFromHTTP("password");
```

The class `phpv\input\KeyValue` (literally a simple bean for a key/value duo) comes with some static helper methods aiming data extraction from the http and cli sapi.

Now let's create the actual input validators and add them to our form validator: 
  
```php
$usernameSizeValidator = new SizeRangeValidator($username, "The username must have a length between 6 and 20", 6, 20);
$usernameTypeValidator = new AlphaNumericalValidator($username, "The username can only contain alphanumerical characters");
$formValidator->addValidator($usernameSizeValidator);
$formValidator->addValidator($usernameTypeValidator);
```

And finally we just need to perform the actual validation on the composite and le jeux sont faits. Here we go:

```php
$output = $formValidator->getValidationOutput();
```

Now `$output` happens to be a `phpv\output\ValidationParcel`. I recommend you have a good look at its interface as it contains importants methods thanks to which you will gain access to the the actual validation results. Here follows an hypothetical simple usage of the `$output` object.

```php
if ($output->isValid()) {
   echo "the data are valid. Thanks!" . PHP_EOL;      
} else {
   echo $output->displayErrors();
}
```

Easy peasy isn't it? Now the method `ValidationParcel::displayErrors` we just used introduces a new important topic: error printers. 

### 6. Error printers

The class `phpv\output\ValidationParcel` *proxies* an internal `phpv\output\printer\ErrorPrinter` object where all the printing responsibilities are offloaded. There are different native printers in _phpv_ and you are also free to create your own extending the abstract class `phpv\output\printer\ErrorPrinter`. The printer proxied by `phpv\output\ValidationParcel` is originally passed in to `phpv\validator\set\KeyValueSetValidator` when one instantiates the main branch validator. By default `phpv\validator\set\KeyValueSetValidator` internally creates a `phpv\output\printer\HTMLErrorPrinter` which is the preferred manner to print HTML markup in a web-based scenario.

At the time of this writing the available native printers are the following:

  * `HTMLErrorPrinter`. Allows to print HMTL markup containing all the errors. The `printErrors` method of this implementation accepts parameters describing how the HTML should be generated. Here's an example:

```php
$branchValidator = new KeyValueSetValidator(new HTMLErrorPrinter() /* this is not necessary as HTMLErrorPrinter is the default! */);
// ... adding leaf validators
$out = $branchValidator->getValidationOutput();
echo $out->printErrors("div|theCSSID|theCSSClass", "span|theCSSClass");

// which will print something of this sort:

<div id='theCSSID'>
   <span class='theCSSClass'>input name 1 - error message 1</span>
   <span class='theCSSClass'>input name 1 - error message 2</span>
   <span class='theCSSClass'>input name 2 - error message 1</span>
   <!-- more -->
</div>
```

I guess it's pretty easy to figure out how it works. The first string passed to `HTMLErrorPrinter::printErrors` describe the `tag`, `id` and `className` of the HTML element wrapping the list of errors. The second one defines only the `tag` and the `className` of each error item. The HTML `id` is not allowed in this context.

  * `JSONErrorPrinter`. This printer should be used in services providing responses to AJAX calls. This printer returns a JSON-like string that can be easily digested by a JavaScript client. Here's how you use it:

```php
$branchValidator = new KeyValueSetValidator(new JSONErrorPrinter());
// ... adding leaf validators
$out = $branchValidator->getValidationOutput();
echo $out->printErrors();

// which will print the following JSON compliant code:
{
   "is-valid": false, 
   "errors": {
      "input-name1": [
         "input-name1 error1",
         "input-name1 error2"],
      "input-name2": "password too short"
      // more
   }
}
```

  * `CLIErrorPrinter`. This printer simply outputs a plain string suitable for validation routines carried out in CLI (command line interface) scripts. Here's a sample:

```php
$branchValidator = new KeyValueSetValidator(new CLIErrorPrinter());
// ... adding leaf validators
$out = $branchValidator->getValidationOutput();
fwrite(STDOUT, $output->displayErrors());

// which would output in your shell plain text of this sort:
# input name 1 - error 1
# input name 1 - error 2
# input name 2 - error 1
# input name 3 - error 1
# more
```

### 7. Error message goodies

When passing in an error message to a `phpv\validator\LeafValidator` you can reference the actual value just inserted with the syntax `%value%`. This placeholder will be resolved right before printing the error messages and hence giving you the power to write more informative messages.

As an example let's consider the case where you have just created an email validator this way:

```php
$mail = KeyValue::obtainFromHTTP("email");
new EmailValidator($mail, "The email %value% you inserted is not valid");
```

Now let's also assume the inserted email is `admin@phpv.x`. When displaying the validation errrors we would obtain (among possible others) the following:

```php
The email admin@phpv.x you inserted is not valid
```

### 8. Preservation of the HTML form state between invalid submission attempts

When a user submits a form and the values don't pass the validation rules, then the form is usually displayed again on page and the submitted values should be displayed in the form so that the user can edit them rather than having to fill in the form from scratch. To help you with that, _phpv_ offers a class called `phpv\validator\set\html\HTMLHelper`. As a matter of fact you will never need to deal with this class directly since the `phpv\validator\set\KeyValueSetValidator` in use for your validation routines is a proxy to all the static methods of such helper. This way you can keep the code in your views pretty lean and with the minimum amount of classes to deal with. We will briefly explore in the following paragraphs what you can achieve thanks to this proxied static interface.

#### 8.1 input and textarea values

Preserving values previously inserted in fields of type `input` (text) and `textarea` is very easy. Here's how you do it:

```php
// this is the composite that we use for our validation process 
$formValidator = new KeyValueSetValidator();
// ... adding leaf validators here
```

Now that we have instanciated a `phpv\validator\set\KeyValueSetValidator` we can use the static methods of `phpv\validator\set\html\HTMLHelper` this class exposes:

```php
<input name="username" value="<?= KeyValueSetValidator::getCollectedValue('username') ?>" />
<textarea name="notes"><?= KeyValueSetValidator::getCollectedValue('notes') ?></textarea>
```

Easy no? All you need to do is passing `KeyValueSetValidator::getCollectedValue` the name of the field you want to retrieve and there it is in yout input (or textarea) field ready for the next submission.

**[!]** Remember that all these static helper methods refer to **the last** instance of `KeyValueSetValidator` that has been created. `KeyValueSetValidator` will keep track of such last instance authomatically so you don't have to worry about it.

#### 8.2 Radio buttons

If it's a radio button, a checkbox or a select you need to monitor, then the html helper will take care of the whole markup generation and not just the preservation of the last value inserted. Here's how you manage radio buttons:

```php
<?= KeyValueSetValidator::radioButton('gender', 'male', true); ?>
<?= KeyValueSetValidator::radioButton('gender', 'female'); ?>
```

As you can see, all you need to do is passing in the `name` attribute of the radio buttons group, the `value` for that specific radio button and a boolean `true` if you want to make the radio button the default at the page's first load.

#### 8.3 Checkboxes

Checkbox is a very similar case:

```php
<?= KeyValueSetValidator::checkbox('swimming', true); ?> swimming
<?= KeyValueSetValidator::checkbox('tennis'); ?> tennis
<?= KeyValueSetValidator::checkbox('golf'); ?> golf
```

Here you only pass in the `name` of the checkbox (used as `id` as well) and a boolean `true` to make the checkbox checked by default at the page's first load.

#### 8.4 Selects 

And finally the tag `select`:

```php
echo KeyValueSetValidator::select("browser", array(
   "b1" => "firefox",
   "b2" => "chrome",
   "b3" => "ie",
   "b4" => "opera",
   "b5" => "safari"
))
```

The method requires the `name` of the select to be printed off (used as `id` as well) and a key/value list for the generation of the various `option` child elements.

It's also woth having a look at the `sample/` folder in the repo and see for yourself some fully working examples showing how the preservation of the inputs' values is achieved.

### 9. Create your own leaf validators

Extending a `phpv\validator\LeafValidator` is very easy. You should choose first the base class that suits your needs the mosts. At the time of this writing you have the following abstract classes to choose among:

  * `phpv\validator\single\KeyValueValidator`: extend this class if you want the most basic validation functionalities applied to a single `phpv\input\KeyValue` object.
  * `phpv\validator\single\IntertwinedFormItemsValidator`: this class can be instantiated using an array of `phpv\input\KeyValue` objects. This way you can actually write rules that focus on the relationship of a bunch of values rather than just the type of a single value like with `phpv\validator\single\KeyValueValidator`.

We will now make a couple of examples showing what an actual implementation would look like. Let's suppose we want to create a validator that makes sure a certain value does not contain swear words. We will extend `phpv\validator\single\KeyValueValidator` and implement the only abstract method that this base class requires: `KeyValueValidator::validate`. This method is where the magic happens. In the body of this method **you MUST register errors** (in case there are any) using the protected method `KeyValueValidator::registerError`. Failing to register errors appearing in such context would result in the specific error not showing up when retrieving errors from `phpv\output\ValidationParcel`. Here's a (well commented) possible implementation:

```php
class BadWordsValidator extends KeyValueValidator {

   private $errorMessage = "";

   // list of bad words to reject
   private $badWords = array(
      "twat",
      "fuck",
      "idiot"
      // etc. 
   );

   public function __construct(KeyValue $kv, $errorMessage) {
      parent::__construct($kv); // KeyValueValidator wants a KeyValue so we pass it in! 
      $this->errorMessage = $errorMessage;
   }

   /**
    * This is the override of the absatract method inherited from the parent!
    * It is where the magic takes place ;)
    */
   public function validate() {
      $containsBadWords = array_search($this->kv->getValue(), $this->badWords);
      if ($containsBadWords === true) {
         $this->registerError($this->errorMessage); // <<< Here we register the error!! 
      }
   }
}
```

Easy peasy isn't it? Ok, let's now move on to an implementation of `phpv\validator\single\IntertwinedFormItemsValidator`. Let's immagine a situation in which we have an HTML form where if the user thicks the checkbox "special-request-wanted" then we want him to fill in a textarea of name "special-request-content" as well. We want to create a validator for this special little system of constraints and here's how we could implement it:

```php
class SpecialRequestsValidator extends IntertwinedFormItemsValidator {

   private $errorMessage = "";

   public function __construct(array $keyValues, $errorMessage) {
      parent::__construct($keyValues);
      $this->errorMessage = $errorMessage;
   }

   public function validate() {
      $checkbox = $this->keyValues["checkbox"];
      $textarea = $this->keyValues["textarea"];
      if ($checkbox->getValue() === "on" /* checkbox have the value set to "on" when selected */) {
         if ($textarea->getValue() === "") {
            $this->registerError($this->errorMessage); // <<< Here we register the error!! 
         }
      }
   }
}
```

And here follows some client code to use this brand new validator:

```php
// after form submission we do ...
$checkbox = KeyValue::obtainFromHTTP("special-request-wanted");
$textarea = KeyValue::obtainFromHTTP("special-request-content");
new SpecialRequestsValidator(array(
   "checkbox" => $checkbox,
   "textarea" => $textarea  
), "You need to specify your special request");
// ... rest of the validation process
```

### 10. A list of all the native validators

In the following table are listed all the leaf validators netively supported by _phpv_. It's an ever evolving list of validators so have a look at it before thinking of extending `phpv\validator\LeafValidator` as you might find in it exactly what you need. 

<table>
   <tr>
      <th>NAME</th>
      <th>DESCRIPTION</th>
      <th>CONSTRUCTOR PARAMETERS</th>
   </tr>
   <tr>
      <td>`AlphaNumericalValidator`</td>
      <td>Check whether or not the `KeyValue` object contains only alphanumerical character (and optionally white space)</td>
      <td>`KeyValue $kv, $errorMessage [, $allowWhiteSpace = false]`</td>
   </tr>
   <tr>
      <td>`AtLeastOnePositiveValidator`</td>
      <td>Check if at least one of the `KeyValue` objects in the provided list is equal to a certain string</td>
      <td>`$keyValues, $errorMessage, $match`</td>
   </tr>
   <tr>
      <td>`DiversityValidator`</td>
      <td>Check if at least one of the `KeyValue` objects in the provided list is equal to a certain string</td>
      <td>`$keyValues, $errorMessage, $match`</td>
   </tr>
   <tr>
      <td>`EmailValidator`</td>
      <td>Check if a `KeyValue` is actually a valid email (optial DNS check available)</td>
      <td>`KeyValue $kv, $errorMessage [, $remoteCheck = false]`</td>
   </tr>
   <tr>
      <td>`EqualityValidator`</td>
      <td>Check whether or not the `KeyValue` is the same as the comparison string provided</td>
      <td>`KeyValue $kv, $errorMessage, $comparison`</td>
   </tr>
   <tr>
      <td>`NumericalValidator`</td>
      <td>Check whether or not the `KeyValue` contains only numerical character (and optionally white space)</td>
      <td>`KeyValue $kv, $errorMessage [, $allowWhiteSpace = false]`</td>
   </tr>
   <tr>
      <td>`OneOfTheseValuesValidator`</td>
      <td>Check if the `KeyValue` contains one of the possible matches provided</td>
      <td>`KeyValue $kv, $errorMessage, array $matches`</td>
   </tr>
   <tr>
      <td>`SizeRangeValidator`</td>
      <td>Check if the `KeyValue` length is between a min and a max</td>
      <td>`KeyValue $kv, $errorMessage, $min, $max`</td>
   </tr>
   <tr>
      <td>`TautologicValidator`</td>
      <td>Does not do anything. The `KeyValue` provided is always valid</td>
      <td>`KeyValue $kv`</td>
   </tr>
<table>

### 11. Running Unit Tests

```
composer run-script test
```
