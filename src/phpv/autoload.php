<?php

spl_autoload_register(function($cn) {
      $pathBits = preg_split("/\\\\/", $cn);
      $fullPath = dirname(__DIR__) . "/" . implode("/", $pathBits) . ".php";
      if (is_readable($fullPath)) {
         require_once($fullPath);
      }
   }, true);