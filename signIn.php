<?php
/**
 * DB: publications
 * Table: finalUsertable(username, password)
 * Table: finalDictionarytable(username, english, translation)
 */
session_start(); 
$conn = new mysqli("localhost", "root", "", "publications");
if($conn->connect_error){
    echo "Connection is failed!!!";
    die("<p><a href=main.php>Click Here To Continue</a></p>");
}

$name = mysqli_real_escape_string($conn, $_POST['user']);
$pass = mysqli_real_escape_string($conn, $_POST['password']);

$s = "SELECT * FROM finalUsertable WHERE username='$name'";
$result = mysqli_query($conn, $s);
if (!$result) {
    echo "Connection is failed!!!";
    die("<p><a href=main.php>Click Here To Continue</a></p>");
}
$num = mysqli_num_rows($result);

if ($num == 1) {
    $row = mysqli_fetch_array($result);
    if (password_verify($pass, $row["password"])) {
        $_SESSION['username'] = $name;
        setcookie("name", $name, time()+3600); // one hour cookie
        header("location:userSystem.php");
    } else {
        echo "Wrong Password!";
        die("<p><a href=main.php>Click Here To Continue</a></p>");
    }
} else {
    echo "Sign Up First!";
    die("<p><a href=main.php>Click Here To Continue</a></p>");
}


?>