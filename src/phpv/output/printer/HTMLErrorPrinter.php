<?php

namespace phpv\output\printer;

use phpv\output\printer\ErrorPrinter;

class HTMLErrorPrinter extends ErrorPrinter {

   const VOID = "*";

   private function getArrayElementSafely($key, array $arr) {
      if (array_key_exists($key, $arr)) {
         if ($arr[$key] === self::VOID) {
            return "";
         } else {
            return $arr[$key];
         }
      }
      return "";
   }

   private function arrayfyData($str, $isWrapper) {
      $bits = null;
      if (substr_count($str, "|") > 0) {
         $bits = explode("|", $str);
      } else {
         $bits = $isWrapper ? array($str, "", "") : array($str, "");
      }
      return $bits;
   }

   private function getTagMarkup($str, $opening, $isWrapper) {
      $bits = $this->arrayfyData($str, $isWrapper);
      $tag = $this->getArrayElementSafely(0, $bits);
      if ($opening === true) {
         if ($isWrapper === true) {
            $id = $this->getArrayElementSafely(1, $bits);
            $class = $this->getArrayElementSafely(2, $bits);
            return "<" . $tag . " id='" . $id . "' class='" . $class . "'>";
         } else {
            $class = $this->getArrayElementSafely(1, $bits);
            return "<" . $tag . " class='" . $class . "'>";
         }
      } else {
         return "</" . $tag . ">";
      }
   }

   private function getOpeningTagMarkup($str, $isWrapper) {
      return $this->getTagMarkup($str, true, $isWrapper);
   }

   private function getClosingTagMarkup($str) {
      return $this->getTagMarkup($str, false, false);
   }

   public function displayErrors($outerHTMLElement = "div", $errorHTMLElement = "p") {
      $html = $this->getOpeningTagMarkup($outerHTMLElement, true);
      foreach ($this->errors as $error) {
         if (is_array($error)) {
            foreach ($error as $errorMsg) {
               $html .= $this->getOpeningTagMarkup($errorHTMLElement, false) . $errorMsg . $this->getClosingTagMarkup($errorHTMLElement);
            }
         } else {
            $html .= $this->getOpeningTagMarkup($errorHTMLElement, false) . $error . $this->getClosingTagMarkup($errorHTMLElement);
         }
      }
      return $html . $this->getClosingTagMarkup($outerHTMLElement);
   }
}