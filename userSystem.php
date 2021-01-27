<?php
/**
 * ###################################################################################
 * upload.php can let a user upload personal defined dictionary for translation.
 * upload.php can let a user translate an English word to Spanish either based on the
 * default dictionary or the uploaded dictionary.
 * Note: The 
 * ###################################################################################
 * DB: publications
 * Table: finalUsertable(username, password)
 * Table: finalDictionarytable(username, english, translation)
 * ###################################################################################
 * Sample of valid dictionary .txt file:
 * hello
 * hola
 * water
 * augar
 * ###################################################################################
 */

session_start();
$conn = new mysqli("localhost","root","","publications");
if($conn->connect_error){
    echo "Connection is failed!!!";
    die("<p><a href=main.php>Click Here To Continue</a></p>");
}

if (!isset($_SESSION['username']) || !isset($_COOKIE['name'])) {
    echo "Click the link below to use our system without logging in:";
    die("<p><a href=publicSystem.php>Click Here To Continue</a></p>");
} else {// HERE IS ELSE

$username = $_SESSION['username'];
echo <<<_END
    <html>
    <head>
        <title>Translation System</title>
    </head>
    <body>
        <h2>Hi '$username'! You Can Start Translating!</h2>
        <form action = "userSystem.php" method = "POST" enctype = "multipart/form-data">
            <p>
            <input type="submit" name="logout" value="LOGOUT">
            </p>
            <br>
            <br>
            <label>Friendly Reminding: Uploaded file should follow this pattern: one line English and the next line translation!</label>
            <br>
            <label>Friendly Reminding: Uploaded .txt file should contain only one word each line!</label>
            <br>
            <label>Friendly Reminding: Upload content-invalid or content-valid files (invalid-format file not included) will delete your previous dictionary!</label>
            <br>
            <label>Friendly Reminding: Remember to check the correctness of your translation by yourself!</label>
            <br><br>
            <label for="file">Upload Dictionary:</label>
            <input type="file" name="dictionary"/>
            <input type="submit" name="upload" value="upload">
            </p>
            <br><br>
            <label for="text">English Word:</label>
            <input type = "text" name="word" id="word">
            <input type="submit" name="translate" value="translate">
        </form>
_END;

if (isset($_POST['logout'])) {
    $_SESSION = array();
    setcookie('name', $username, time()-2592000, '/');
    unset($_SESSION['username']);
    session_destroy();
    header('location: main.php');
}
// translate in two conditions: default or self defined dictionary
if (array_key_exists('translate', $_POST) && isset($_POST['translate'])) {
    $english = mysqli_real_escape_string($conn, $_POST['word']);
    translate($username, $english);
}

$english = "";
$translation = "";
// Get information from uploaded dictionary file
if (array_key_exists('upload', $_POST) && isset($_POST['upload'])) {
    if ($_FILES) {
        // Some File properties
        $fileName = $_FILES['dictionary']['name'];
        $fileExt = explode('.', $fileName);
        $fileExt = strtolower(end($fileExt));
        // Allowed file type
        $allowed = array('txt');
        if (!($_FILES['dictionary']['error']===0)) {//check file error
            echo "<br>There was an error uploading your file!<br>";
        } else if (!in_array($fileExt, $allowed)) {//check file type
            echo "<br>You cannot upload files of this type!<br>";
        } else if ($_FILES['dictionary']['size'] >= 1000000) {
            echo "<br>Your file is too big!<br>";
        } else {
            //Delete previous dictionary data
            $delete_sql = "DELETE FROM finalDictionarytable WHERE username='$username'";
            $delete = mysqli_query($conn, $delete_sql);
            if(!$delete) {
                die("<br>Deletion failed!<br>");
            }
            $fp = fopen($_FILES['dictionary']['tmp_name'], 'rb');
            while ($line=fgets($fp)) {
                $line = preg_replace("/[ \t]+/", "", preg_replace("/\s*/m", "", $line));
                $line = strtolower(trim($line));
                if (!ctype_alpha($line)) {//check for invalid English word
                    echo "<br>Invalid English word, check file!<br>";
                    $english = "";
                    $translation = "";
                    //Delete previous insertion if the file is invalid
                    $delete_sql = "DELETE FROM finalDictionarytable WHERE username='$username'";
                    $delete = mysqli_query($conn, $delete_sql);
                    if(!$delete) {
                        die("<br>Deletion failed!<br>");
                    }
                    exit(0);
                }
                $line = sanitizeMySQL($conn, $line);
                $english = $line;
                if(!$line=fgets($fp)) {//check for unmatched english word
                    echo "<br>Missing translation, check your file!<br>";
                    $english = "";
                    $translation = "";
                    //Delete previous insertion if the file is invalid
                    $delete_sql = "DELETE FROM finalDictionarytable WHERE username='$username'";
                    $delete = mysqli_query($conn, $delete_sql);
                    if(!$delete) {
                        die("<br>Deletion failed!<br>");
                    }
                    exit(0);
                }
                $line = preg_replace("/[ \t]+/", "", preg_replace("/\s*/m", "", $line));
                $line = strtolower(trim(sanitizeMySQL($conn, $line)));
                $translation = $line;
                $insert_sql = "INSERT INTO finalDictionarytable(username, english, translation) VALUES ('".$username."','".$english."', '".$translation."')";
                $insert = mysqli_query($conn, $insert_sql);
                if(!$insert) {
                    die("<br>Insertion failed!<br>");
                }
            }
            $conn->close();
        }
    }
}        
    


}//HERE IS ELSE
echo "</body></html>";

#####################################################################################
function translate($username, $english) {
    $conn = new mysqli("localhost","root","","publications");
    if($conn->connect_error){
        echo "Connection is failed!!!";
    }

    $english = strtolower(trim($english));
    if($english==='') {
        echo "<br>Input should not be empty!<br>";
        exit(0);
    }
    if (!ctype_alpha($english)) {
        echo "<br>Invalid English word, please check spelling!<br>";
        exit(0);
    }
    // Begin translate. Try personal dictionary first before default dictionary
    $query1 = "SELECT translation FROM finalDictionarytable WHERE username='$username' and english='$english'";
    $result1 = mysqli_query($conn, $query1);
    if (!$result1) {
        echo "<br>Connection is failed! Try to translate one more time!<br>";
        exit(0);
    }
    $row = mysqli_fetch_array($result1);
    //Try default dictionary
    if (empty($row) || !isset($row) || is_null($row)) {
        $query2 = "SELECT translation FROM finalDictionarytable WHERE username='default' and english='$english'";
        $result2 = mysqli_query($conn, $query2);
        if (!$result2) {
            echo "<br>Connection is failed! Try to translate one more time!<br>";
            exit(0);
        }
        $row = mysqli_fetch_array($result2);
        if (empty($row) || !isset($row) || is_null($row)) {
            echo "<br>Cannot find the corresponding translation!<br>";
            exit(0);
        }
        $translation = $row["translation"];
        $translation = strtolower(trim($translation));
        echo "<br>The Spanish translation of English word '$english' is: $translation<br>";
    } else {
        $translation = $row["translation"];
        $translation = strtolower(trim($translation));
        echo "<br>The Spanish translation of English word '$english' is: $translation<br>";
    }
    $conn->close();
}

####################################################################################
function sanitizeString($var) {
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

function sanitizeMySQL($conn, $var) {
    $var = $conn->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}

?>