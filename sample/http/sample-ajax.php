<?php
include_once '../../src/phpv/autoload.php';

use phpv\validator\set\KeyValueSetValidator;
?>

<!DOCTYPE html>

<html lang="en">

   <head>

      <title>phpv example</title>
      <link rel="stylesheet" type="text/css" href="common.css" />

   </head>
   <body>

      <h1>phpv : form validation example - AJAX</h1>

      <p id="errors-container" class="errors" style="display: none;">

      </p>

      <form method="post" action="#" id="ajax-form">

         <img id="loader" src="loader.gif" class="loading-stuff" />
         <div id="cover" class="loading-stuff"></div>

         <img id="logo" src="../../../icon.png" alt="" />

         <div class="col">
            <label>your name:</label>
            <input type="text" name="name" />

            <label>your password:</label>
            <input type="password" name="password" />

            <label>your gender:</label>
            <input type="radio" name="gender" value="m" checked="checked" /> male
            <input type="radio" name="gender" value="f" /> female
            <input type="radio" name="gender" value="non" /> non-permitted-value

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
            <textarea name="thoughts" rows="5" cols="40"></textarea>

            <label>What sports do you like?</label>
            <input type="checkbox" name="football" /> football
            <input type="checkbox" name="tennis" /> tennis
            <input type="checkbox" name="swimming" /> swimming
         </div>

      </form>

      <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

      <script>
      
         (function($) {
            
            var errorsBox = $("#errors-container");
            var loadingStuff = $(".loading-stuff");
            
            $("#ajax-form").bind("submit", function(e) {
               e.preventDefault();
               loadingStuff.show();
               errorsBox.hide();
               $.ajax({
                  dataType: "json",
                  url: "process-ajax.php",
                  type: "POST",
                  data: $(this).serialize(),
                  success: function(data) {
                     if (data["is-valid"] === false) {
                        var html = "";
                        var errors = data["errors"];
                        for (var p in errors) {
                           var error = errors[p];
                           if (typeof error === "string") {
                              html += "<span class='error'>" + error + "</span>";
                           } else {
                              for (var i = 0, l = error.length; i < l; i++) {
                                 html += "<span class='error'>" + error[i] + "</span>";                                 
                              }
                           }                           
                        }
                        errorsBox.html(html);
                        errorsBox.fadeIn();
                        loadingStuff.hide();
                     } else {
                        alert("You made it! Form will be submitted...");
                     }
                  }
               });   
            });

         })(jQuery);      
      
      </script>

   </body>
</html>