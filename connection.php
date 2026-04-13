<?php

$userid = 'root'; //stores database in a variable called userid
//root is the default mySQL admin user 
$password = ''; //password set to empty - on a real site this would be initailised for security reasoning

//connects to the MySQL database called striver on this computer
$connection = "mysql:host=localhost;dbname=striver;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //if an error occurs it throws and exception instead of failing quietly
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //controls how results are returned - as an associative array
];

try {
    $pdo = new PDO($connection, $userid, $password, $options); //create the database connection
} catch (PDOException $error) {
    die('Database connection failed: ' . $error->getMessage()); //if it fails it shows the error message and stops the connection
}
?>