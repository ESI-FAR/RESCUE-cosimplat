<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set headers to return JSON and avoid caching
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cosimplat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Decode the JSON payload sent from client-side
$data = json_decode(file_get_contents('php://input'), true);

// Validate JSON decode
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON payload"]);
    exit();
}

// Prepare SQL statement for insertion
$sql = "INSERT INTO simcrono (simgame_id, submodel_id, payload, state_history, modified)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit();
}

// Bind parameters and execute the statement
$stmt->bind_param("iisss", $data['simgame_id'], $data['submodel_id'], $data['payload'], $data['state_history'], $data['modified']);

if (!$stmt->execute()) {
    echo json_encode(["error" => "SQL execute failed: " . $stmt->error]);
    exit();
}

// Close statement and connection
$stmt->close();
$conn->close();

// Return success response if insertion was successful
echo json_encode(["message" => "Row inserted successfully"]);

?>
