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
            <div class="title">
                <img src="school-logo.png" alt="School Logo">
                <h1>SAMS</h1>
                <h2>School Attendance Management System</h2>
            </div>
        </div>
        <div class="form-container">
            <button class="parent-button">TEACHERS</button>
            <form method="POST" action="login.php">
                <label for="student-id">ID Number</label>
                <input type="text" id="id_number" name="id_number" placeholder="Enter ID Number" required>

                <label for="pin">Password</label>
                <input type="password" id="tpassword" name="tpassword" placeholder="Enter Password" required>

                <button type="submit" class="login-button">Log In</button>
            </form>
            <div>
                <a href="index1.php"><h3>Sign Up?</h3></a>
            </div>
        </div>
    </div>
</body>
</html>
