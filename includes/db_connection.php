<?php
// Database configuration
$dbHost = 'localhost';   // Replace with your database host
$dbUser = 'root';        // Replace with your database username
$dbPass = '786$toqA'; // Replace with your database password
//$dbPass = '';
$dbName = 'bill';   // Replace with your database name

// Create a new database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
