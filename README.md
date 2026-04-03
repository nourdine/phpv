PHPV
====

### 1. Intro

**Phpv** is a data validation framework. It can be used in a wide range of scenarios and **not necessarilly** for classic form data validation over http. The library has been conceived with the [Composite Pattern](http://en.wikipedia.org/wiki/Composite_pattern) in mind. This implies that the validation of a single piece of data, or the validation of a group of them, can be carried out uniformely and taking exactly the same actions on the involved objects.

### 2. Validator

The interface `Phpv\Validator\Validator` is the very core of the library. As the name suggests, this interface provides the capability of validating data. The library contains various implementations of this interface which will help you carrying out your basic validation routines. At the same time, you will be able to add your own validators and mix them up with the native ones that are shipped with the library (see paragraph 6 for more).

### 3. Branches and Leafs

**The composite patterns** states that "the whole" can be treathed exactly like "the parts" composing it. A branch will naturally contain leafs, and any operation carried out on the branch will be automatically reflected on the leafs as well.

The same can be said about the validators provided in this library: there are **branch validators** which can carry other validators (see `Phpv\Validator\Branch\BranchValidator`) and **leaf validators** (see `Phpv\Validator\Leaf\LeafValidator` and its subclasses), which, instead, cannot contain any additional child validator.

Validating a branch validator will cause the contained leaf validators to be validated as well. A global validation execution output will be provided as a result.

### 4. A real world example

Let's say we want to validate a signup form containing a `username` and a `password`. We will start by creating a branch validator which we will fill with a bunch of leaf validators later on:

```php
use Phpv\Input\KeyValue;
use Phpv\Validator\Branch\BranchValidator;
use Phpv\Validator\Leaf\Native\SizeRangeValidator;
use Phpv\Validator\Leaf\Native\AlphaNumericalValidator;

$formValidator = new BranchValidator();
```

For a start, we need to get the values of `username` and `password` from the http request. This is an context specific step that we can avoid here for clarity's sake and simply hardcode the values instead:

```php
// values need to be wrapped up in a `KeyValue` object
$username = new KeyValue("username", "user&]"); // this contains special characters
$password = new KeyValue("password", "shortpass"); // this is too short
```

Let's now create the actual input validators and add them to our form validator:

```php
$formValidator->addValidator(new SizeRangeValidator($username, "The username must have a length between 8 and 20", 8, 16));
$formValidator->addValidator(new AlphaNumericalValidator($username, "The username can only contain alphanumerical characters"));
$formValidator->addValidator(new SizeRangeValidator($password, "The password must have a length between 10 and 20", 12, 30));
```

And finally we just need to take the actual validation step on the composite and we are done. Here we go:

```php
$output = $formValidator->validate();
```

The variable `$output` happens to be an instance of `Phpv\Output\ValidationParcelProxy`. I recommend you have a good look at this interface as it contains importants methods thanks to which you will gain access to the results of the validation process.

Here follows an hypothetical simple usage of the `$output` object.

```php
if ($output->isValid()) {
   echo "The provided data are valid. Thanks!" . PHP_EOL;
} else {
   print_r($output->getStackedErrorMessages());
}
```

Because the provided values are not valid, we will get a bunch of errors:

```php
Array
(
    [username] => Array
        (
            [0] => The username must have a length between 8 and 16
            [1] => The username can only contain alphanumerical characters
        )

    [password] => The password must have a length between 12 and 30
)
```

As you can see, the error messages are grouped by the input name they refer to. In our case the `username` is not valid because of its length and composition, whereas the `password` only because of its length.

### 5. Error message goodies

When passing an error message to a `phpv\validator\LeafValidator`, you can reference the actual user-provided value by using the syntax `%value%`. This placeholder will be resolved right before returning the error message and hence giving you the power to write more informative messages.

As an example, let's consider the following snippet where an email validator is created:

```php
$email = new KeyValue("email", "admin@phpv.");
$validator = new EmailValidator($email, "The provided email '%value%' is not valid");
print_r($validator->validate()->getStackedErrorMessages());
```

This will print the following error message:

```php
Array
(
    [email] => The provided email 'admin@phpv.' is not valid
)
```

### 6. Create your own leaf validators

Extending a `phpv\validator\LeafValidator` is very easy an there are basically two abstract classes which you might want to start from:

  1. `phpv\validator\Leaf\KeyValueValidator`: extend this class if you want the most basic validation functionalities applied to a single `phpv\input\KeyValue` object.

  2. `phpv\validator\Leaf\IntertwinedKeyValuesValidator`: this class takes an array of `phpv\input\KeyValue` objects. It basically allows you to write rules that focus on the relationship between a bunch of values rather than just one single value.

Alright, let's move on to a couple of real examples.

#### 6.1. BadWordsValidator

Let's suppose we want to create a validator that makes sure a certain value does not contain swear words. We will extend `phpv\validator\Leaf\KeyValueValidator` and implement the only abstract method that this base class requires: `KeyValueValidator::doValidate`.

This method is where the magic happens. In the body of this method **you must register errors** (in case you found any) using the inherited method `parent::registerError`. Failing to register errors will result in the validator not doing anything. Here's a possible implementation:

```php
class BadWordsValidator extends KeyValueValidator
{
   // list of bad words to reject
   private $badWords = [
      "fool",
      "stupid",
      "idiot",
      // etc. 
   ];

   public function __construct(KeyValue $kv, string $errorMessage)
   {
      parent::__construct($kv, $errorMessage);
   }

   public function doValidate(): void
   {
      foreach ($this->badWords as $bw) {
         if (strpos($this->kv->getValue(), $bw)) {
            $this->registerError($this->errorMessage); // let's register the error!
            return;
         }
      }
   }
}

$v = new BadWordsValidator(new KeyValue("description", "Someone is a fool around here!"), "Please do not swear");
print_r($v->validate()->getStackedErrorMessages());
```

This will result in the following:

```php
Array
(
    [description] => Please do not swear
)
```

Easy, isn't it?

#### 6.2. Validate a constraint between multiple values

Let's now move on to an implementation of `phpv\validator\Leaf\IntertwinedKeyValuesValidator`. Let's immagine we have an HTML form where, if the user thicks the checkbox `special-request-wanted`, then we expect him to fill in a textarea named `special-request-text` as well. We want to create a validator for this special little system of constraints and here's how we could do that:

```php
class SpecialRequestValidator extends IntertwinedKeyValuesValidator
{
   public function __construct(array $keyValues, string $errorMessage)
   {
      parent::__construct($keyValues, $errorMessage);
   }

   public function doValidate(): void
   {
      $checkbox = $this->keyValues["checkbox"];
      $textarea = $this->keyValues["textarea"];
      if ($checkbox->getValue() === "on" && $textarea->getValue() === "") {
         $this->registerError($this->errorMessage);
      }
   }
}
```

And here follows some client code that uses our new validator:

```php
$checkbox = new KeyValue("special-request-wanted", "on");
$textarea = new KeyValue("special-request-text", "");
$v = new SpecialRequestValidator(array(
   "checkbox" => $checkbox,
   "textarea" => $textarea
), "You must describe your special request");

print_r($v->validate()->getStackedErrorMessages());
```

Which will print the following:

```php
Array
(
    [merged-name:special-request-wanted|special-request-text] => You must describe your special request
)
```

### 7. A list of the native validators

In the following table are listed all the leaf validators netively supported by the library. It's an ever evolving list of validators so have a look at it before thinking of extending `phpv\validator\LeafValidator`. You might end up finding exactly what you need.

<table>
   <tr>
      <th>Name</th>
      <th>Description</th>
      <th>Constructor signature</th>
   </tr>

   <tr>
      <td>RequiredValidator</td>
      <td>Check if the provided value is non empty</td>
      <td><pre>KeyValue $kv, string $errorMessage</pre>
          Expected data type contained in the KeyValues: mixed<br/>
          <b>Note:</b> the value is validated using the empty() function
      </td>
   </tr>

   <tr>
      <td>AlphaNumericalValidator</td>
      <td>Check if the provided value contains only alphanumerical characters (and optionally white space)</td>
      <td><pre>KeyValue $kv, string $errorMessage [, bool $whiteSpaceAllowed = false]</pre>
          Expected data type contained in the KeyValue object: string
      </td>
   </tr>

   <tr>
      <td>NumericalValidator</td>
      <td>Check if the provided value contains only numerical characters (and optionally white space)</td>
      <td><pre>KeyValue $kv, string $errorMessage [, bool $whiteSpaceAllowed = false]</pre>
         Expected data type contained in the KeyValue object: string
      </td>
   </tr>

   <tr>
      <td>EmailValidator</td>
      <td>Check if the provided value is a valid email address</td>
      <td><pre>KeyValue $kv, $errorMessage</pre>
          Expected data type contained in the KeyValue object: string
      </td>
   </tr>

   <tr>
      <td>SizeRangeValidator</td>
      <td>Check if the length of the provided value is between a min and a max</td>
      <td><pre>KeyValue $kv, string $errorMessage, int $min, int $max</pre>
          Expected data type contained in the KeyValue object: string
      </td>
   </tr>

   <tr>
      <td>EqualityValidator</td>
      <td>Check if the provided value is identical to a comparison value</td>
      <td><pre>KeyValue $kv, string $errorMessage, mixed $comparison</pre>
          Expected data type contained in the KeyValue object: mixed<br/>
          Expected data type of $comparison: mixed<br/>
          <b>Note:</b> the values are compared using the === operator so only primitive types (string, bool, int, float and null) will work as expected
      </td>
   </tr>

   <tr>
      <td>DiversityValidator</td>
      <td>Check if the provided value is different from a comparison value</td>
      <td><pre>KeyValue $kv, string $errorMessage, mixed $comparison</pre>
          Expected data type contained in the KeyValue object: mixed<br/>
          Expected data type of $comparison: mixed<br/>
          <b>Note:</b> the values are compared using the === operator so only primitive types (string, bool, int, float and null) will work as expected
      </td>
   </tr>

   <tr>
      <td>MatchAnyOf</td>
      <td>Check if at least one of the values in the provided list is equal to the provided target</td>
      <td><pre>array $keyValues, string $errorMessage, mixed $target</pre>
          Expected data type contained in the KeyValue objects: mixed<br/>
          Expected data type of $target: mixed<br/>
          <b>Note:</b> the values are compared using the === operator so only primitive types (string, bool, int, float and null) will work as expected
      </td>
   </tr>

   <tr>
      <td>InValidator</td>
      <td>Check if the provided value matches at least one of the provided targets</td>
      <td><pre>KeyValue $kv, string $errorMessage, array $targets</pre>
          Expected data type contained in the KeyValue objects: mixed<br/>
          Expected data type of the items in $targets: mixed<br/>
          <b>Note:</b> the values are compared using the === operator so only primitive types (string, bool, int, float and null) will work as expected
      </td>
   </tr>
</table>

### 8. Run unit tests

```
composer install
composer test
```
