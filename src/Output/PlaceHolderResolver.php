<?php

declare(strict_types=1);

namespace Phpv\Output;

class PlaceHolderResolver
{
   const PH = "%value%";

   public static function containsPlaceHolder(string $str)
   {
      return preg_match('/' . self::PH . '/', $str) > 0;
   }

   public static function resolvePlaceHolder(string $str, string $value)
   {
      if (self::containsPlaceHolder($str)) {
         $str = preg_replace('/' . self::PH . '/', $value, $str);
      }
      return $str;
   }
}
