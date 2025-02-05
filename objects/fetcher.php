<?php

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set headers to return JSON and avoid caching
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Database configuration
$servername = "mariadb";
$username = "user";
$password = "userpassword";
$dbname = "cosimplat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Get lastTimestamp from the request
$lastTimestamp = isset($_GET['lastTimestamp']) ? $_GET['lastTimestamp'] : 0;

// Set the maximum wait time (in seconds)
$maxWaitTime = 30;
$startTime = time();
$data = [];

// Poll for new data until maxWaitTime is reached
do {
    // SQL query to fetch new rows where simgame_id = 1 and timestamp is greater than lastTimestamp
    $sql = "SELECT timestamp, payload FROM simcrono WHERE simgame_id = 1 AND timestamp > ? ORDER BY timestamp ASC";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $lastTimestamp);

    if (!$stmt->execute()) {
        echo json_encode(["error" => "SQL execute failed: " . $stmt->error]);
        exit();
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        break; // Exit the loop if new data is found
    }

    $stmt->close();

    // Sleep for a short period to prevent hammering the database
    usleep(500000); // Sleep for 0.5 seconds
} while (time() - $startTime < $maxWaitTime);

$conn->close();

echo json_encode($data);
?>
