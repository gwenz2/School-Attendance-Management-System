<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Database connection
$servername = "localhost"; // Update with your server name
$username = "root";        // Update with your DB username
$password = "";            // Update with your DB password
$dbname = "sams";          // Update with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student ID is provided in the URL
if (isset($_GET['s_id'])) {
    $s_id = $conn->real_escape_string($_GET['s_id']);

    // First, delete related notifications for this student
    $delete_notifications_sql = "DELETE FROM notifications WHERE student_id = '$s_id'";
    if (!$conn->query($delete_notifications_sql)) {
        echo "<script>
                alert('Error deleting related notifications: " . $conn->error . "');
                window.location.href = 'main.php';
              </script>";
        exit();  // Stop further processing if deleting notifications fails
    }

    // Now, delete the student record from the student_info table
    $delete_student_sql = "DELETE FROM student_info WHERE s_id = '$s_id'";

    if ($conn->query($delete_student_sql) === TRUE) {
        echo "<script>
                alert('Student deleted successfully!');
                window.location.href = 'main.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $conn->error . "');
                window.location.href = 'main.php';
              </script>";
    }
} else {
    // Redirect if no student ID is provided
    header("Location: main.php");
}

$conn->close();
?>
