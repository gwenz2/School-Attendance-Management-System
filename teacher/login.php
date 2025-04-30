<?php
// Database connection
$servername = "localhost"; // Update this with your server name
$username = "root";        // Update this with your database username
$password = "";            // Update this with your database password
$dbname = "sams";     // Update this with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $conn->real_escape_string($_POST['id_number']);
    $tpassword = $conn->real_escape_string($_POST['tpassword']);

    // Query to check credentials
    $sql = "SELECT * FROM `user` WHERE `id_number` = '$id_number' AND `tpassword` = '$tpassword'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login successful
        session_start();
        $_SESSION['user_id'] = $id_number; // Store user ID in session
        header("Location: dashboard/main.php"); // Redirect to dashboard
        exit();
    } else {
        // Login failed
        echo "<script>alert('Invalid ID Number or Password'); window.location.href='index.php';</script>";
    }
}

$conn->close();
?>
