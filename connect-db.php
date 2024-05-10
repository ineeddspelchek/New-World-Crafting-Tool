<?php
session_start();
// Sources:
// https://stackoverflow.com/questions/1035634/converting-an-integer-to-a-string-in-php



// error_reporting(E_ALL);
// ini_set("display_errors", 1);

/** S24, PHP (on GCP, local XAMPP, or CS server) connect to MySQL (on CS server) **/
$username = ''; 
$password = '';
$host = '';
$dbname = '';
$dsn = "mysql:host=$host;dbname=$dbname";

try
{
    //$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db = new PDO($dsn, $username, $password);

   // dispaly a message to let us know that we are connected to the database 
   //echo "<p>You are connected to the database -- host=$host</p>";
}
catch (PDOException $e)     // handle a PDO exception (errors thrown by the PDO library)
{
   // Call a method from any object, use the object's name followed by -> and then method's name
   // All exception objects provide a getMessage() method that returns the error message 
   $error_message = $e->getMessage();
   echo "<p>An error occurred while connecting to the database: $error_message </p>";
}
catch (Exception $e)       // handle any type of exception
{
   $error_message = $e->getMessage();
   echo "<p>Error message: $error_message </p>";
}


//Based on query helper function from Professor Hott's PL for Web Apps
function query($sql, ...$params) {
    try {
        global $db; 
        $statement = $db->prepare($sql);
        foreach ($params as $key => $param) {
            $statement->bindValue($key+1, $param);
        }
        $statement->execute();
        $res = $statement->fetchAll();
        $statement->closeCursor();
        return $res;
    }
    catch (PDOException $e) {
        echo $sql;
        echo "<pre>" . $e->getMessage() . "</pre>";
    } 
    catch (Exception $e) {
        echo $sql;
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
}
?>

