<?php
session_start();

// Database connection settings
$host = "localhost";
$username = "root";
$password = ""; // Update this if your MySQL server has a password
$dbname = "sams";

// Initialize variables
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $_POST["student-id"];
    $pin = $_POST["pin"];

    // Establish database connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM student_info WHERE s_id = ? AND s_pin = ?");
    $stmt->bind_param("ss", $student_id, $pin);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching record exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc(); // Fetch the row
        $_SESSION["student_id"] = $student_id;
        $_SESSION["created_by"] = $row["created_by"]; // Assuming the column is named `created_by`
        header("Location: main.php");
        exit;
    }
     else {
        $error = "Invalid Student ID or PIN.";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMS - School Attendance Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="school-logo.png" alt="School Logo">
            </div>
            <div class="title">
                <h1>SAMS</h1>
                <h2>School Attendance Management System</h2>
            </div>
        </div>
        <div class="form-container">
            <button class="parent-button">PARENTS</button>
            <form method="POST" action="">
                <label for="student-id">Student ID:</label>
                <input type="text" id="student-id" name="student-id" placeholder="Enter Student ID" required>

                <label for="pin">PIN:</label>
                <input type="password" id="pin" name="pin" placeholder="Enter PIN" required>

                <button type="submit" class="login-button">Log In</button>
            </form>
            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
