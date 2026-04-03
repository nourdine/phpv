<?php

declare(strict_types=1);

namespace Phpv\Output;

class Error
{
   public readonly string $fieldName;
   public readonly string $fieldValue;
   public readonly string $message;

   public function __construct(string $invalidFieldName, string $invalidFieldValue, string $errorMessage)
   {
      $this->fieldName = $invalidFieldName;
      $this->fieldValue = $invalidFieldValue;
      $this->message = $errorMessage;
   }   
}
