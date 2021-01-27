<?php
/**
 * ###################################################################################
 * main.php includes the HTML for main page before signing in or signing up
 * ###################################################################################
 * Run following queries in your terminal to create a table to store user information:
 * mysql -u root -p
 * CREATE database publications;
 * USE publications;
 * CREATE TABLE finalUsertable(
 * username VARCHAR(128) not null,
 * password VARCHAR(128) not null,
 * primary key(username)
 * );
 * ###################################################################################
 * Run following queries to create a table to store uploaded dictionary information:
 * CREATE TABLE finalDictionarytable(
 * username VARCHAR(128),
 * english VARCHAR(128),
 * translation VARCHAR(128)
 * );
 * ###################################################################################
 * The following information is included in my default dictionary (for testing):
 * This dictionary is stored in the DB with "username" = "default"
 * English       Spanish
 * ---------------------------
 * hello         hola
 * one           uno
 * two           dos
 * three         tres
 * restaurant    restaurante
 * water         agua
 * money         dinero
 * sun           dom
 * moon          luna
 * star          estrella
 * ###################################################################################
 */
session_start();
$conn = new mysqli("localhost","root","","publications");
if($conn->connect_error){
    echo "Connection is failed!!!";
    die("<p><a href=main.php>Click Here To Continue</a></p>");
}

echo <<<_END
<html>
<head>
    <title> Translation System </title>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2> Login Here </h2>
            <form action="signIn.php" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="user" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary"> Login </button>
            </form>
        </div>
        <div class="col-md-6">
            <h2> Register Here </h2>
            <form action="signUp.php" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="user" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary"> Register </button>
            </form>
        </div>
    </div>
</div>
_END;

echo "<br>Click the the following link to continue to our translation system directly:<br>";

if (isset($_COOKIE['name']) || isset($_SESSION['username'])) {
    die("<p><a href=userSystem.php>Click Here To Continue</a></p>");
} else {
    die("<p><a href=publicSystem.php>Click Here To Continue</a></p>");
}


echo "</body></html>";

?>