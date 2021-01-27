<?php
/**
 * DB: publications
 * Table: finalUsertable(username, password)
 * Table: finalDictionarytable(username, english, translation)
 */

$conn = new mysqli("localhost", "root", "", "publications");
if($conn->connect_error){
    die("Connection is failed!!!");
    die("<p><a href=main.php>Click Here To Continue</a></p>");
}

$name = mysqli_real_escape_string($conn, $_POST['user']);
$pass = mysqli_real_escape_string($conn, $_POST['password']);
$hashedpass = password_hash($pass, PASSWORD_DEFAULT);

$s = "SELECT * FROM finalUsertable WHERE username = '$name'";
$result = mysqli_query($conn, $s);
if (!$result) {
    echo "Connection is failed!!!";
    die("<p><a href=main.php>Click Here To Continue</a></p>");
}
$num = mysqli_num_rows($result);

if ($num == 1) {
    echo "Username Was Already Taken!";
    die("<p><a href=main.php>Click Here To Continue</a></p>");
} else {
    $reg = "INSERT INTO finalUsertable(username, password) VALUES ('$name', '$hashedpass')";
    $result = mysqli_query($conn, $reg);
    if (!$result) {
        echo "Connection is failed!!!";
        die("<p><a href=main.php>Click Here To Continue</a></p>");
    } else {
        echo "Registration Successfully! Go Sign In!";
        die("<p><a href=main.php>Click Here To Continue</a></p>");
    }
}

?>