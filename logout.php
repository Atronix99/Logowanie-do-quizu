<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "loginy";
$conn = mysqli_connect($host, $user, $pass, $db);

if (isset($_SESSION['user_id'])) {
    mysqli_query($conn, "UPDATE uzytkownicy SET token_logowania = NULL WHERE id = " . $_SESSION['user_id']);
}

setcookie('remember_me', '', time() - 3600, "/");
session_unset();
session_destroy();

header("Location: index.php");
exit();
?>