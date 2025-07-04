<?php 
   session_start();
   unset($_SESSION['logged-in']);
   header("Location:../profile.php?id=9");
   ?>