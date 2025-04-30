<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit;
}

// Database connection settings
$host = "localhost";
$username = "root";
$password = ""; // Update with your database password if needed
$dbname = "sams";

// Retrieve logged-in student ID and created_by
$student_id = $_SESSION['student_id'];
$created_by = $_SESSION['created_by'];

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student and parent information
$stmt = $conn->prepare("SELECT s_name, p_name FROM student_info WHERE s_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $student_name = $row['s_name'];
    $parent_name = $row['p_name'];
} else {
    $student_name = "Unknown Student";
    $parent_name = "Unknown Parent";
}
$stmt->close();

// Fetch subject name using created_by
$stmt = $conn->prepare("SELECT sub_name FROM user WHERE id_number = ?");
$stmt->bind_param("i", $created_by);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $subject_name = $row['sub_name'];
} else {
    $subject_name = "Unknown Subject";
}

// Fetch notifications for the logged-in student
$notifications_sql = "SELECT * FROM notifications WHERE student_id = ? ORDER BY date DESC LIMIT 5";
$stmt = $conn->prepare($notifications_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$notifications_result = $stmt->get_result();

// Fetch the number of unread notifications
$unread_sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE student_id = ? AND status = 'unread'";
$stmt_unread = $conn->prepare($unread_sql);
$stmt_unread->bind_param("i", $student_id);
$stmt_unread->execute();
$unread_result = $stmt_unread->get_result();
$unread_row = $unread_result->fetch_assoc();
$unread_count = $unread_row['unread_count'];

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absent Request</title>
    <link rel="stylesheet" href="styles1.css">
    <style>
        /* Header Bar */
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px 10px 20px; /* top, right, bottom, left */    
        }

        /* Notification Icon */
        #notification-icon {
            position: relative;
            cursor: pointer;
        }

        /* Notification Count */
        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px;
            font-size: 12px;
        }

        /* Notification Dropdown */
        .notification-dropdown {
            display: none;
            position: absolute;
            top: 40px; /* Adjust dropdown position */
            right: 0;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            padding: 10px;
        }

        .notification-dropdown a {
            padding: 8px;
            display: block;
            text-decoration: none;
            color: #000;
        }

        .notification-dropdown a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <!-- Header displaying the parent and student names -->
    <div class="header-bar">
        <span class="header-title">
            <?php echo htmlspecialchars($parent_name); ?> [<?php echo htmlspecialchars($student_name); ?>'S PARENT'S]
        </span>
        <!-- Notification Icon -->
        <div id="notification-icon">
            <img src="notification-bell.png" alt="Notifications" width="30">
            <span class="notification-count"><?php echo $unread_count; ?></span> <!-- Dynamic notification count -->
        </div>
        <!-- Notification Dropdown -->
        <div class="notification-dropdown" id="notification-dropdown">
            <?php if ($notifications_result->num_rows > 0): ?>
                <?php while ($notification = $notifications_result->fetch_assoc()): ?>
                    <a href="#"><?php echo htmlspecialchars($notification['message']); ?></a>
                <?php endwhile; ?>
            <?php else: ?>
                <a href="#">No notifications</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main content -->
    <div class="container">
        <h1 class="form-title">ABSENT REQUEST</h1>
        <form action="submit_absence.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="subject-name">Subject Name:</label>
                <span id="subject-name" class="subject-name"><?php echo htmlspecialchars($subject_name); ?></span>
                <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>">
            </div>
            <div class="form-group">
                <label for="topic">Topic:</label>
                <input type="text" id="topic" name="topic" placeholder="Enter topic" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" placeholder="Enter description" required></textarea>
            </div>
            <div class="form-buttons">
                <input type="file" class="file-button" name="attachment" accept="image/*">
                <button type="submit" class="submit-button">Submit</button>
            </div>
        </form>
    </div>

    
    <script src="notification.js"></script>
    <script>
        // Toggle the notification dropdown
        document.getElementById('notification-icon').addEventListener('click', function() {
            var dropdown = document.getElementById('notification-dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
    </script>
</body>
</html>
