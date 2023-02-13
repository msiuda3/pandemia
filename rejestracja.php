<?php session_start(); ?>

<html>
  <head>
    <title>Rejestracja</title>
  </head>

  <body>

  <?php
      if(isset($_SESSION['register_failed']) && $_SESSION['register_failed'] == true){
        echo 'BłĄD REJESTRACJI: ' . $_SESSION['register_failed_message'];
      }
      else{
      }
     ?>

    <form action="register.php" method="POST">


    Imię : <input type="text" name="name"/> </br>
    Nazwisko : <input type="text" name="surname"/> </br>
    Adres : <input type="text" name="address"/> </br>
    Typ konta : <select name="account_type" id="account_type">
    <option value="taker">Zleceniobiorca</option>
    <option value="creator">Zleceniodawca</option>
  </select> </br>
</br>
    Login : <input type="text" name="login"/> </br>
    Hasło: <input type="password" name="password"/> </br>
    Powtórz hasło: <input type="password" name="password_confirm"/> </br>
    <input type="submit" value="Zarejestruj nowe konto"/> </br>

    </form>

  <div id="register">Masz już konto?<a href="login-page.php">Zaloguj się!</a></div></br>
  </body>


</html>
