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

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Teacher Dashboard</title>
    <style>
        /* Basic styling for the modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
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
        <a href=""><h4 id="studentRecord">Student Record</h4></a>
        <a href="main2.php"><h4 id="room">Attendance</h4></a>
        <a href="main3.php"><h4 id="room">Absence Request</h4></a>
    </nav>

    <!-- Main Content -->
    <main>
        <section id="userManagement">
            <h1>Student Records</h1>
            <button id="addStudentBtn">Add Student</button>

            <table>
                <tr>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Parents</th>
                    <th>PIN</th>
                    <th>Action</th>
                </tr>
                <?php
                // Fetch and display student records created by the logged-in user
                $conn = new mysqli($servername, $username, $password, $dbname);
                $sql = "SELECT * FROM student_info WHERE created_by = '$user_id'"; // Filter by created_by
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['s_id']}</td>
                            <td>{$row['s_name']}</td>
                            <td>{$row['p_name']}</td>
                            <td>{$row['s_pin']}</td>
                            <td>
                                <a href='delete_student.php?s_id={$row['s_id']}'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No records found.</td></tr>";
                }

                $conn->close();
                ?>
            </table>
        </section>
    </main>

    <!-- Modal for Adding Student -->
    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Student</h2>
            <form method="POST" action="add_student.php">
                <label for="s_id">Student ID:</label>
                <input type="text" id="s_id" name="s_id" required><br><br>
                
                <label for="s_name">Student Name:</label>
                <input type="text" id="s_name" name="s_name" required><br><br>

                <label for="p_name">Parent Name:</label>
                <input type="text" id="p_name" name="p_name" required><br><br>

                <label for="s_pin">PIN:</label>
                <input type="text" id="s_pin" name="s_pin" required><br><br>

                <button type="submit">Add Student</button>
            </form>
        </div>
    </div>
    <script src="notification.js"></script>
    <script>
        // Get modal and button
        const modal = document.getElementById("addStudentModal");
        const btn = document.getElementById("addStudentBtn");
        const span = document.getElementsByClassName("close")[0];

        // Open the modal
        btn.onclick = () => { modal.style.display = "block"; };

        // Close the modal
        span.onclick = () => { modal.style.display = "none"; };

        // Close if clicked outside modal
        window.onclick = (event) => {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>
