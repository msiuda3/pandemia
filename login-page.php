<?php session_start(); ?>

<html>
  <head>
    <title>Zaloguj się</title>
  </head>

  <body>

    <?php
      if(isset($_SESSION['login_failed']) && $_SESSION['login_failed'] == true){
        echo 'BłĄD LOGOWANIA!';
      }
      else{
      }
     ?>

    <form action="login.php" method="POST">


      Login : <input type="text" name="login"/> </br>
      Hasło: <input type="password" name="password"/> </br>
      <input type="submit" value="Zaloguj się"/> </br>

    </form>

  <div id="login"><a href="rejestracja.php">Zarejestruj się</a></div></br>
  </body>


</html>
