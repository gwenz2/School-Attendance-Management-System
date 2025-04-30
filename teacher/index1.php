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
            <form method="POST" action="register.php">
                <label for="fname">Fullname</label>
                <input type="text" id="fname" name="fname" placeholder="Joy" required>

                <label for="mnumber">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" placeholder="09345678901" required>

                <label for="checkin">Subject Name</label>
                <input type="text" id="sub_name" name="sub_name" placeholder="ex. Math" required><br>

                <label for="pin">Password</label>
                <input type="password" id="signUpPassword" name="signUpPassword" placeholder="Password" required>

                <button type="submit" class="login-button">Sign Up</button>
            </form>
            <div>
                <a href="index.php"><h3>Login?</h3></a>
            </div>
        </div>
    </div>
</body>
</html>