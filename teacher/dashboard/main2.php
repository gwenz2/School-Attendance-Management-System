<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
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

// Sanitize user input
$user_id = $conn->real_escape_string($_SESSION['user_id']);

// Fetch user details
$sql = "SELECT * FROM `user` WHERE `id_number` = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = htmlspecialchars($row['fullname']);
    $sub_name = htmlspecialchars($row['sub_name']);
} else {
    $fullname = "Unknown User";
    $sub_name = "";
}

// Fetch students from the database
$students_result = $conn->query("SELECT * FROM student_info WHERE created_by = '$user_id'");

// Handle attendance marking (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    $date = $conn->real_escape_string($_POST['date']);
    $created_by = $user_id;

    // Loop through the students and mark attendance
    foreach ($_POST['status'] as $student_id => $status) {
        $student_name = $conn->real_escape_string($_POST['student_name'][$student_id]);
        $status = $conn->real_escape_string($status);

        // Check if attendance record exists
        $check_query = "SELECT * FROM attendance WHERE s_id = '$student_id' AND date = '$date' AND sub_name = '$sub_name'";
        $check_result = $conn->query($check_query);

        if ($check_result->num_rows > 0) {
            echo "<script type='text/javascript'>
                localStorage.setItem('pNotify', 'true');
            </script>";
            // Update the existing record
            $update_query = "UPDATE attendance 
                             SET status = '$status', created_by = '$created_by' 
                             WHERE s_id = '$student_id' AND date = '$date' AND sub_name = '$sub_name'";
            $conn->query($update_query);
        } else {
            echo "<script type='text/javascript'>
                localStorage.setItem('pNotify', 'true');
            </script>";
            // Insert a new record
            $insert_query = "INSERT INTO attendance (s_id, s_name, sub_name, date, status, created_by) 
                             VALUES ('$student_id', '$student_name', '$sub_name', '$date', '$status', '$user_id')";
            $conn->query($insert_query);
        }

        // Insert notification based on attendance status
        $notification_message = "";
        switch ($status) {
            case 'present':
                $notification_message = "Your child was marked as Present for the subject $sub_name on $date.";
                break;
            case 'late':
                $notification_message = "Your child was marked as Late for the subject $sub_name on $date.";
                break;
            case 'excuse':
                $notification_message = "Your child was marked as Excused for the subject $sub_name on $date.";
                break;
            case 'absent':
                $notification_message = "Your child was marked as Absent for the subject $sub_name on $date.";
                break;
            default:
                $notification_message = "Attendance status for your child in $sub_name has been updated.";
        }
        

        // Insert the notification into the database
        $notification_query = "INSERT INTO notifications (student_id, message, status, date) 
                               VALUES ('$student_id', '$notification_message', 'unread', NOW())";
        $conn->query($notification_query);
    }

    // Redirect after processing the form
    header("Location: main2.php"); // Redirect to refresh the page or show the results
    exit();
}

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
        <img id="logo" src="school-logo.png" alt="School Logo">
        <h3 id="logot">SAMS - School Attendance Management System</h3>
        <a href="logout.php" class="logout-btn" style="float: right;">Logout</a>
    </header>

    <!-- Sidebar Navigation -->
    <nav id="navSide">
        <label>Hi, <?php echo $fullname; ?></label>
        <a href="main.php"><h4 class="student-record">Student Record</h4></a>
        <a href="main2-1.php"><h4 class="room">View Attendance</h4></a>
        <a href="main3.php"><h4 class="room">Absence Request</h4></a>
    </nav>

    <!-- Main Content -->
    <main>
        <section id="userManagement">
            <h1>Mark Attendance: <?php echo $sub_name; ?></h1>

            <!-- Attendance Form -->
            <h3>Mark Attendance</h3>
            <br>
            <form method="POST" action="">
                <label for="date">Date:</label>
                <input type="date" name="date" required><br><br>
                <table>
                    <tr>
                        <th>Student Name</th>
                        <th>Status</th>
                    </tr>
                    <?php while ($student = $students_result->fetch_assoc()) { ?>
                        <tr>
                            <td>
                                <input type="hidden" name="student_name[<?php echo $student['s_id']; ?>]" value="<?php echo $student['s_name']; ?>">
                                <?php echo $student['s_name']; ?>
                            </td>
                            <td>
                                <select name="status[<?php echo $student['s_id']; ?>]">
                                    <option value="present">Present</option>
                                    <option value="late">Late</option>
                                    <option value="excuse">Excuse</option>
                                    <option value="absent">Absent</option>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <br>
                <button type="submit" name="attendance">Submit Attendance</button>
            </form>
        </section>
    </main>
    <script src="notification.js"></script>
</body>
</html>
