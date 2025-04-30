<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Database connection
$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "sams";          

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT fullname FROM `user` WHERE `id_number` = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = $row['fullname'];
} else {
    $fullname = "Unknown User";
}

// Handle status update and notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && isset($_POST['request_id'])) {
    $status = $_POST['status'];
    $request_id = $_POST['request_id'];

    // Update the absence request status
    $update_sql = "UPDATE absence_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();
    $stmt->close();

    // Fetch the student's parent details to send the notification
    $fetch_request_sql = "SELECT si.p_name AS parent_name, ar.student_id, ar.subject_name 
                          FROM absence_requests ar
                          JOIN student_info si ON ar.student_id = si.s_id
                          WHERE ar.id = ?";
    $stmt = $conn->prepare($fetch_request_sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request_result = $stmt->get_result();
    $request_row = $request_result->fetch_assoc();
    $parent_name = $request_row['parent_name'];
    $student_id = $request_row['student_id'];
    $subject_name = $request_row['subject_name'];

    // Create the notification message based on the status
    switch ($status) {
        case 'approve':
            $notification_message = "Your child’s absence request for the subject $subject_name has been approved.";
            break;
        case 'disapprove':
            $notification_message = "Your child’s absence request for the subject $subject_name has been disapproved.";
            break;
        case 'pending':
            $notification_message = "Your child’s absence request for the subject $subject_name is still pending.";
            break;
        default:
            $notification_message = "The status of your child’s absence request for $subject_name has been updated.";
    }

    // Check if a notification already exists for this student and request ID
    $check_notification_sql = "SELECT id FROM notifications 
                               WHERE student_id = ? AND message LIKE ?";
    $stmt = $conn->prepare($check_notification_sql);
    $stmt->bind_param("is", $student_id, $notification_message);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script type='text/javascript'>
                localStorage.setItem('pNotify', 'true');
            </script>";
        // Update the existing notification if it exists
        $update_notification_sql = "UPDATE notifications 
                                    SET message = ?, status = 'unread' 
                                    WHERE student_id = ? AND message LIKE ?";
        $stmt = $conn->prepare($update_notification_sql);
        $stmt->bind_param("sis", $notification_message, $student_id, $notification_message);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<script type='text/javascript'>
                localStorage.setItem('pNotify', 'true');
            </script>";
        // Insert the notification if it does not exist
        $insert_notification_sql = "INSERT INTO notifications (student_id, message, status) 
                                    VALUES (?, ?, 'unread')";
        $stmt = $conn->prepare($insert_notification_sql);
        $stmt->bind_param("is", $student_id, $notification_message);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch absence requests for this teacher
$sql = "SELECT 
            ar.id, 
            ar.subject_name, 
            ar.topic, 
            ar.description, 
            ar.attachment_path, 
            ar.request_date, 
            ar.status, 
            si.s_name AS student_name, 
            si.p_name AS parent_name, 
            u.fullname AS created_by
        FROM 
            absence_requests ar
        JOIN 
            student_info si ON ar.student_id = si.s_id
        JOIN 
            user u ON ar.created_by = u.id_number
        WHERE 
            ar.created_by = ?
        ORDER BY 
            ar.request_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Teacher Dashboard</title>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <img id="logo" src="school-logo.png">
        <h3 id="logot">SAMS - School Attendance Management System</h3>
        <a href="logout.php" class="logout-btn" style="float: right;">Logout</a>
    </header>

    <!-- Sidebar Navigation -->
    <nav id="navSide">
        <label>Hi, <?php echo htmlspecialchars($fullname); ?></label>
        <a href="main.php"><h4 id="studentRecord">Student Record</h4></a>
        <a href="main2.php"><h4 id="room">Attendance</h4></a>
        <a href=""><h4 id="room">Absence Request</h4></a>
    </nav>

    <!-- Main Content -->
    <main>
        <section id="userManagement">
            <h1>Absence Request</h1>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Parent Name</th>
                        <th>Subject</th>
                        <th>Topic</th>
                        <th>Description</th>
                        <th>Attachment</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['topic']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <?php if ($row['attachment_path']): ?>
                                        <a href="<?php echo 'http://localhost/sams/teacher/' . htmlspecialchars($row['attachment_path']); ?>" target="_blank">View File</a>
                                    <?php else: ?>
                                        No Attachment
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                        <select name="status">
                                            <option value="approve" <?php echo $row['status'] === 'approve' ? 'selected' : ''; ?>>Approve</option>
                                            <option value="disapprove" <?php echo $row['status'] === 'disapprove' ? 'selected' : ''; ?>>Disapprove</option>
                                            <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" style="width: max-content; padding:4px;">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">No absence requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
    <script src="notification.js"></script>
</body>
</html>
