<?php
/**
 * ###################################################################################
 * upload.php can let a user upload personal defined dictionary for translation
 * upload.php can let a user translate an English word to Spanish either based on the
 * default dictionary or the uploaded dictionary
 * ###################################################################################
 * DB: publications
 * Table: finalUsertable(username, password)
 * Table: finalDictionarytable(username, english, translation)
 */

echo <<<_END
<html>
    <head>
        <title>Translation System</title>
    </head>
    <body>
        <h2>You Can Start Translating!</h2>
        <form action="publicSystem.php" method = "POST">
        English Word: <input type="text" name="word">
        <input type="submit" value="translate" name="translate">
        </form>
_END;

if(array_key_exists('translate', $_POST)) {
    translate();
}
echo "</body></html>";
#####################################################################################
function translate() {
    $conn = new mysqli("localhost","root","","publications");
    if($conn->connect_error){
        echo "Connection is failed!!!";
        die("<p><a href=main.php>Click Here To Continue</a></p>");
    }

    if(isset($_POST['translate'])) {
        $english = mysqli_real_escape_string($conn, $_POST['word']);
        $english = strtolower(trim($english));
        if($english==='') {
            echo "<br>Input should not be empty!<br>";
            exit(0);   
        }
        if (!ctype_alpha($english)) {
            echo "<br>Invalid English word, please check spelling!<br>";
            exit(0);
        }
        $s = "SELECT * FROM finalDictionarytable WHERE username='default' and english='$english'";
        $result = mysqli_query($conn, $s);    
        if (!$result) {
            echo "<br>Connection is failed! Try to translate one more time!<br>";
            exit(0);
        }
        $row = mysqli_fetch_array($result);
        if (empty($row) || !isset($row) || is_null($row)) {
            echo "<br>Cannot find the corresponding translation!<br>";
            exit(0);
        }
        $translation = $row["translation"];
        $translation = strtolower(trim($translation));
        echo "<br>The Spanish translation of English word '$english' is: $translation<br>";
    }
    $conn->close();
}

?>