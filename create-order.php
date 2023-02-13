<?php
ob_start();
include_once "db-config.php";

session_start();


// Create connection
$conn = new mysqli(SERVER_NAME, SERVER_USERNAME, SERVER_PASSWORD, DB_NAME);
// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO orders (person_id)
VALUES (".$_SESSION['person_id'].")";

if ($conn->query($sql) === TRUE) {
 echo "New record created successfully";
} else {
 echo "Error: " . $sql . "<br>" . $cwonn->error;
}

$orderId = $conn->query("SELECT last_insert_id() AS id")->fetch_row()[0];

$items = $_POST['items'];

foreach($items as $itemId=>$itemAmount){
  if(isset($itemId) AND $itemAmount != ''){
    $sql = "INSERT INTO order_items (order_id, item_id, amount)
    VALUES (".$orderId.", ". $itemId.", ". $itemAmount.")";
    if ($conn->query($sql) === TRUE) {
     echo "New record created successfully";
    } else {
     echo "Error: " . $sql . "<br>" . $conn->error;
   }
  }

}




$conn->close();
header("Location: index.php");
?>
