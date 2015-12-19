<?php

namespace phpv\output;

class PlaceHolderResolver {

   const PH = "%value%";

   public static function containPlaceHolder($str) {
      return preg_match('/' . self::PH . '/', $str) > 0;
   }

   public static function resolvePlaceHolder($str, $value) {
      if (self::containPlaceHolder($str)) {
         $str = preg_replace('/' . self::PH . '/', $value, $str);
      }
      return $str;
   }
}