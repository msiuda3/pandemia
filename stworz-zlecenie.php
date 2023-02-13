<?php
include_once "db-config.php";

session_start();

  function getItems(){
    // Create connection
    $conn = new mysqli(SERVER_NAME, SERVER_USERNAME, SERVER_PASSWORD, DB_NAME);
    // Check connection
    if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, name FROM items";
    $result = $conn->query($sql);

    $conn->close();


if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $items = array();

  while($row = mysqli_fetch_assoc($result)) {
    array_push($items, array('id' => $row["id"], 'name' => $row["name"]));
  }
  return $items;
} else {
  return array();
}


  }


 ?>

<html>
  <head>
    <title>Stwórz zlecenie</title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php
    if(!isset($_SESSION['person_id']) || $_SESSION['person_id'] == ''){

      header('Location: login-page.php');
    }

    echo '<div id="login">'.$_SESSION['person_name'] . ' ' . $_SESSION['person_surname'] . ' <a href="logout.php">wyloguj się</a></div></br>';

     ?>

    <form action='create-order.php' method = 'post'>

      <table>
        <tr>
          <td>
            Produkt
          </td>
          <td>
            Ilość
          </td>

        </tr>
        <?php
          $items = getItems();
          for($i = 0; count($items) > $i; $i++){

            echo '<tr>
            <td>
            ' . $items[$i]['name']  . ':
            </td>
            <td>
            <input type="text" name="items['.$items[$i]['id'].']"/>
            </td>
            </tr>';
          }
         ?>


      </table>
      <input type="submit" value = "potwierdź">
    </form>

  </body>


</html>
