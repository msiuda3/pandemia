<?php
ob_start();
include_once "db-config.php";

session_start();

$servername = SERVER_NAME;
$username = SERVER_USERNAME;
$password = SERVER_PASSWORD;
$dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO taken_orders (order_id, person_id)
VALUES ('".$_POST['order_id']."', '".$_SESSION['person_id']."')";

if ($conn->query($sql) === TRUE) {
 echo "New record created successfully";
} else {
 echo "Error: " . $sql . "<br>" . $conn->error;
}



$conn->close();


header("Location: index.php");
?>
