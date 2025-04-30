<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sams";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data from POST request
$fullname = $_POST['fname'];
$mnumber = $_POST['mobile'];
$sub_name = $_POST['sub_name'];
$tpassword = $_POST['signUpPassword'];

// Prepare the SQL statement to avoid SQL injection
$stmt = $conn->prepare("INSERT INTO user (fullname, mnumber, sub_name, tpassword) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $fullname, $mnumber, $sub_name, $tpassword);

// Execute the query
if ($stmt->execute()) {
    $test = $conn->insert_id;
    echo "New record created successfully! Your ID is {$test}";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
