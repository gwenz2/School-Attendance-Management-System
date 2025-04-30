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

// Handle date filtering
$filter_date = isset($_GET['filter_date']) ? $conn->real_escape_string($_GET['filter_date']) : '';

// Modify attendance query to filter by date if a date is selected
if (!empty($filter_date)) {
    $attendance_query = "SELECT * FROM attendance WHERE sub_name = '$sub_name' AND date = '$filter_date' ORDER BY date DESC";
} else {
    $attendance_query = "SELECT * FROM attendance WHERE sub_name = '$sub_name' ORDER BY date DESC";
}
$attendance_result = $conn->query($attendance_query);

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
        <a href="main2.php"><h4 class="room">Mark Attendance</h4></a>

        <a href="main3.php"><h4 class="room">Absence Request</h4></a>
    </nav>

    <!-- Main Content -->
    <main>
        <section id="userManagement">
            <h1>View Attendance: <?php echo $sub_name; ?></h1>

            <!-- Date Filter Form -->
            <form method="GET" action="main2-1.php">
                <label for="filter_date">Filter by Date:</label>
                <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                <button type="submit">Filter</button>
            </form>

            <!-- View Attendance Records -->
            <h3>Attendance Records</h3>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Status</th>
                </tr>
                <?php if ($attendance_result->num_rows > 0) { ?>
                    <?php while ($attendance = $attendance_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attendance['date']); ?></td>
                            <td><?php echo htmlspecialchars($attendance['s_name']); ?></td>
                            <td><?php echo htmlspecialchars($attendance['status']); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No attendance recorded in this Date</td>
                    </tr>
                <?php } ?>
            </table>
        </section>
    </main>
    <script src="notification.js"></script>
</body>


</html>
