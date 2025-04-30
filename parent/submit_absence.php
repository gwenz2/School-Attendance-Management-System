<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

// Database connection settings
$host = "localhost";
$username = "root";
$password = ""; // Update with your database password
$dbname = "sams";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve session values
$student_id = $_SESSION['student_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input to prevent SQL injection
    $subject_name = $_POST['subject_name'];
    $topic = $_POST['topic'];
    $description = $_POST['description'];
    $attachment_path = null;

    // Handle file upload
    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = "../teacher/uploads/";

        // Check if the uploads directory exists, create if not
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);  // Create directory if it doesn't exist
        }

        $file_name = basename($_FILES['attachment']['name']);
        $target_path = $upload_dir . $file_name;

        // Validate file type (optional, depending on your requirements)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            die("Error: Unsupported file type.");
        }

        // Move the uploaded file
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_path)) {
            $attachment_path = $target_path;
        } else {
            die("Error: Unable to upload file.");
        }
    }

    // Lookup `created_by` based on `student_id`
    $lookup_sql = "SELECT created_by FROM student_info WHERE s_id = ?";
    $stmt = $conn->prepare($lookup_sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($created_by);
    $stmt->fetch();
    $stmt->close();

    if ($created_by) {
        echo "<script type='text/javascript'>
                localStorage.setItem('tNotify', 'true');
            </script>";
        // Insert absence request
        $insert_sql = "INSERT INTO absence_requests 
                       (student_id, subject_name, topic, description, attachment_path, created_by) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("issssi", $student_id, $subject_name, $topic, $description, $attachment_path, $created_by);

        if ($stmt->execute()) {
            echo "Absence request added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: No user associated with the given student ID.";
    }
}

// Close the database connection
$conn->close();
?>
