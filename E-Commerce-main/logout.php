<?php
   session_start();
   session_unset();
   session_destroy();
   header("location:../Online-Food-Ordering-System-in-PHP-main\login.php?SuccessfullyLoggedout");
 ?>