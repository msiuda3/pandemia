<?php
include_once "db-config.php";

session_start();


  function getOrders(){
    // Create connection
    $conn = new mysqli(SERVER_NAME, SERVER_USERNAME, SERVER_PASSWORD, DB_NAME);
    // Check connection
    if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT orders.id, users.name, users.surname, users.address, JSON_OBJECTAGG(items.name, order_items.amount) AS items
     FROM orders
      JOIN order_items ON order_items.order_id = orders.id 
       JOIN items ON order_items.item_id = items.id
        JOIN users ON users.id = orders.person_id
        WHERE orders.id NOT IN (SELECT order_id FROM taken_orders)
         GROUP BY orders.id;"; // bierzemy tylko zlecenia, ktore nie sa juz wziete
    $result = $conn->query($sql);

    $orders = array();

    while($row = mysqli_fetch_assoc($result)) {
      $itemsResult = json_decode($row['items']);
      $items = array();

      foreach ($itemsResult as $itemName => $itemAmount) {
        array_push($items, array('name' => $itemName, 'amount' => $itemAmount));
      }

      array_push($orders, array(
        'id' => $row["id"],
        'person' => array('name' => $row['name'], 'surname' => $row['surname'], 'address' => $row['address']),
        'items' => $items
      )
    );
    }

    return $orders;

  }

  function getOrderHtml($order){

    $result = '';
    $result .= $order['person']['name'] . ' ' . $order['person']['surname'] . '<br> ';
    $result .= $order['person']['address'];

    $items = $order['items'];
    $result .= '<table>';

    for($i = 0; count($items) > $i; $i++){
      $result .= '<tr>
        <td>
          ' . $items[$i]['name'] . ':
        </td>
        <td>
        ' . $items[$i]['amount'] . '
        </td>
      </tr>
      ';

    }
    $result .= '</table>';
    return $result;
  }

 ?>

<html>
  <head>
    <title>Weź zlecenie</title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php
    if(!isset($_SESSION['person_id']) || $_SESSION['person_id'] == ''){

      header('Location: login-page.php');
    }

    echo '<div id="login">'.$_SESSION['person_name'] . ' ' . $_SESSION['person_surname'] . ' <a href="logout.php">wyloguj się</a></div></br>';

     ?>


    <table>

      <?php
        $orders = getOrders();
        echo '<table>';
        for($i = 0; count($orders) > $i; $i++){

          echo '<tr>
          <td>
          ' . getOrderHtml($orders[$i]) . ':
          </td>
          <td>
          <form action="take-order.php" method="POST"><input type="hidden" name="order_id" value="'.$orders[$i]['id'].'""/><input type="submit" value="Weź""/></form>
          </td>
          </tr>';
        }
        echo '</table>';
       ?>


    </table>

  </body>


</html>
