<?php
session_start();
?>

<html>
<head>
  <title>Pandemia</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php
include_once 'db-config.php';

  if(!isset($_SESSION['person_id']) || $_SESSION['person_id'] == ''){

    header('Location: login-page.php');
  }

  echo '<div id="login">'.$_SESSION['person_name'] . ' ' . $_SESSION['person_surname'] . ' <a href="logout.php">wyloguj się</a></div></br>';


  $userId = $_SESSION['person_id'];

  function checkUserType($userId){

    $conn = new mysqli(SERVER_NAME, SERVER_USERNAME, SERVER_PASSWORD, DB_NAME);
    // Check connection
    if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT type FROM users WHERE id = '".$userId."'";
    $result = $conn->query($sql);
    $conn->close();
    return $result->fetch_row()[0];
  }

  if(checkUserType($userId) == 'taker'){
    //OSOBA REALIZUJACA
    echo '<a href="wez-zlecenie.php">Przeglądaj zlecenia</a></br>';

    function getOrdersTaken(){
      // Create connection
      $conn = new mysqli(SERVER_NAME, SERVER_USERNAME, SERVER_PASSWORD, DB_NAME);
      // Check connection
      if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
      }

      $sql = "SELECT orders.id, creators.name, creators.surname, creators.address, JSON_OBJECTAGG(items.name, order_items.amount) AS items
       FROM orders
        JOIN order_items ON order_items.order_id = orders.id
         JOIN items ON order_items.item_id = items.id
          JOIN users AS creators ON creators.id = orders.person_id
           JOIN taken_orders ON taken_orders.order_id = orders.id
            JOIN users AS takers ON takers.id = taken_orders.person_id
             WHERE takers.id = '".$_SESSION['person_id']."' AND taken_orders.resolved = '0'  GROUP BY orders.id, taken_orders.id;";
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

    function getTakenOrderHtml($order){
      $result = '';


      $items = $order['items'];
      $result .= 'Dla: '. $order['person']['name'].' '.$order['person']['surname'] . ' do ' . $order['person']['address'];
      $result .= '<table id ="test">';

      for($i = 0; count($items) > $i; $i++){
        $result .= '<tr>
          <td>
            ' . $items[$i]['name'] . '
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

    $ordersTaken = getOrdersTaken();

    echo '<table>';

    foreach ($ordersTaken as $key => $orderTaken) {
      echo '<tr> '. getTakenOrderHtml($orderTaken) . '</tr>';
      echo '-------- </br>';
    }

    echo '</table>';
  }

  else{
    // OSOBA ZLECAJACA
    echo '<a href="stworz-zlecenie.php">Stwórz nowe zlecenie</a> </br>';

    function getOrdersCreated(){

      // Create connection
      $conn = new mysqli(SERVER_NAME, SERVER_USERNAME, SERVER_PASSWORD, DB_NAME);
      // Check connection
      if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
      }

      $sql = "SELECT orders.id, creators.name AS creators_name, creators.surname AS creators_surname, creators.address AS creators_address, JSON_OBJECTAGG(items.name, order_items.amount) AS items, takers.name AS takers_name, takers.surname AS takers_surname
       FROM orders
        JOIN order_items ON order_items.order_id = orders.id
         JOIN items ON order_items.item_id = items.id
          JOIN users AS creators ON creators.id = orders.person_id
           LEFT JOIN taken_orders ON taken_orders.order_id = orders.id
            LEFT JOIN users AS takers ON takers.id = taken_orders.person_id
             WHERE creators.id = '".$_SESSION['person_id']."' AND (taken_orders.resolved IS NULL OR taken_orders.resolved = '0')
 GROUP BY orders.id, taken_orders.id;";
      $result = $conn->query($sql);
      $orders = array();

      $status;
      $taker = '';

      while($row = mysqli_fetch_assoc($result)) {

        if(isset($row['takers_name'])){
          $status = 'taken';
          $taker = $row['takers_name'].' '.$row['takers_surname'];
        }
        else{
          $status = 'pending';

        }

        $itemsResult = json_decode($row['items']);
        $items = array();

        foreach ($itemsResult as $itemName => $itemAmount) {
          array_push($items, array('name' => $itemName, 'amount' => $itemAmount));
        }

        array_push($orders, array(
          'id' => $row["id"],
          'status' => $status,
          'order_taker' => $taker,
          'person' => array('name' => $row['creators_name'], 'surname' => $row['creators_surname'], 'address' => $row['creators_address']),
          'items' => $items
        )
      );
      }

      return $orders;
    }

    function getCompleteOrderButtonHtml($order){
        return '<form action="resolve-order.php" method="POST">
        <input type="hidden" name="order_id" value="'.$order['id'].'"/>
        <input type="submit" value="Zalicz"/>
        </form>';

    }


    function getOrderHtml($order){
      $result = '';

      if($order['status'] == 'taken'){
        $result .= 'Status: Wzięte przez ' . $order['order_taker'] . getCompleteOrderButtonHtml($order);
      }
      else{
        $result .= 'Status: Czeka na wzięcie';

      }

      $items = $order['items'];
      $result .= '<table id ="test">';

      for($i = 0; count($items) > $i; $i++){
        $result .= '<tr>
          <td>
            ' . $items[$i]['name'] . '
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

    $ordersCreated = getOrdersCreated();

    echo '<table>';

    foreach ($ordersCreated as $key => $orderCreated) {
      echo '<tr> '. getOrderHtml($orderCreated) . '</tr>';
      echo '-------- </br>';
    }

    echo '</table>';
  }



 ?>
</body>
 </html>
