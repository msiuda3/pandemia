
<?php
session_start();
include_once "db-config.php";

// Create connection
$conn = new mysqli(SERVER_NAME, SERVER_USERNAME, SERVER_PASSWORD, DB_NAME);
// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}




$login = $_POST['login'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$name = $_POST['name'];
$surname = $_POST['surname'];
$address = $_POST['address'];
$type = $_POST['account_type'];


if($name == '' OR $surname == '' OR $address == ''){
    $_SESSION['register_failed'] = true;
    $_SESSION['register_failed_message'] = 'Należy wypełnić wszystkie pola!';
    header("Location: rejestracja.php");
    exit();
}


if(!isUsernameValid($login, $conn)){
    $_SESSION['register_failed'] = true;
    $_SESSION['register_failed_message'] = 'Istnieje już konto z takim loginem!';
    exit();

}



$sql = "INSERT INTO users (login, password, name, surname, address, type) VALUES ('".$login."',  '".md5($password)."', '".$name."', '".$surname."', '".$address."', '".$type."')";



if ($conn->query($sql) === TRUE) {
     header("Location: login-page.php");
   } else {
    $_SESSION['register_failed'] = true;
    $_SESSION['register_failed_message'] = "Wystąpił nieoczekiwany błąd. Spróbuje ponownie.";
    header("Location: rejestracja.php");
  }

$conn->close();




function isUsernameValid($login, $conn){
    $sql = "SELECT id FROM users WHERE login = '".$_POST['login']."'";
    $result = $conn->query($sql);
    return $result->num_rows == 0;

}

function isPasswordValid($password, $password_confirm){
    return $password == $password_confirm;

}


?>
