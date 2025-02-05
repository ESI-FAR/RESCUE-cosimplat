<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set headers to return a response in plain text
header('Content-Type: text/plain');

// Database configuration
$servername = "mariadb";
$username = "user";
$password = "userpassword";
$dbname = "cosimplat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the table to truncate
$tableName = "simcrono"; // Change this to your table name

// Prepare SQL statement for truncation
$sql = "TRUNCATE TABLE $tableName";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Table '$tableName' truncated successfully.";
} else {
    echo "Error truncating table: " . $conn->error;
}

// Close the connection
$conn->close();

?>
