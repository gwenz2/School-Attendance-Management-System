<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$servername = "localhost"; // Update with your server name
$username = "root";        // Update with your DB username
$password = "";            // Update with your DB password
$dbname = "sams";          // Update with your DB name

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$s_id = $_POST['s_id'];
$s_name = $_POST['s_name'];
$p_name = $_POST['p_name'];
$s_pin = $_POST['s_pin'];
$created_by = $_SESSION['user_id']; // Set the created_by as logged-in user_id

// Insert student record into database
$sql = "INSERT INTO student_info (s_id, s_name, p_name, s_pin, created_by) 
        VALUES ('$s_id', '$s_name', '$p_name', '$s_pin', '$created_by')";

if ($conn->query($sql) === TRUE) {
    echo "New student added successfully";
    header("Location: main.php"); // Redirect back to main page after insertion
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
