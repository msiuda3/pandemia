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

$sql = "SELECT id, name, surname FROM users WHERE login = '".$_POST['login']."' AND password = '".md5($_POST['password'])."'";
echo $sql;
$result = $conn->query($sql);

$conn->close();

if($result->num_rows > 0){
  $row = $result->fetch_row();
  $_SESSION['person_id'] = $row[0];
  $_SESSION['person_name'] = $row[1];
  $_SESSION['person_surname'] = $row[2];
  header("Location: index.php");
}
else{
  $_SESSION['login_failed'] = true;
  header("Location: login-page.php");
}

?>
