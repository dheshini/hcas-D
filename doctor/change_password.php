<?php
session_start();
include '../config.php';

// Handle password change form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    // Validate current password, new password, and confirm password
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify if the new password is different from the current password
    if ($new_password === $current_password) {
        // New password is the same as the current password
        echo "<script>alert('New password must be different from the current password.');</script>";
    } else {
        // Fetch the current user's stored password from the database
        $email = $_SESSION['email'];
        $sql = "SELECT password FROM doctors WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];
            
            // Verify if the current password matches the stored password
            if (password_verify($current_password, $stored_password)) {
                // Current password matches, proceed to update the password
                if ($new_password === $confirm_password) {
                    // Hash the new password
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update the password in the database
                    $sql = "UPDATE doctors SET password = ? WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $hashed_new_password, $email);
                    if ($stmt->execute()) {
                        // Password updated successfully, redirect to success page
                        header("location: password_change_success.php");
                        exit();
                    } else {
                        // Error updating password
                        echo "<script>alert('Error updating password. Please try again.');</script>";
                    }
                } else {
                    // New password and confirm password do not match
                    echo "<script>alert('New password and confirm password do not match.');</script>";
                }
            } else {
                // Current password provided by the user is incorrect
                echo "<script>alert('Incorrect current password.');</script>";
            }
        } else {
            // User not found in the database
            header("location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCTOR - CHANGE PASSWORD</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Times;
            margin: 0;
            padding: 8;
            background-image: url('../image/color1.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        header {
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 36px;
            font-family: 'Segoe UI', Times, Geneva, Verdana, sans-serif;
        }

        nav {
            background: rgba(0, 0, 0, 0.5);
            overflow: hidden;
            padding: 15px 0;
            text-align: center;
        }

        nav a {
            display: inline-block;
            color: #fff;
            text-align: center;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        #sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            top: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.8);
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
            text-align: left;
        }

        #sidebar a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: 0.3s;
        }

        #sidebar a:hover {
            color: #3498db;
        }

        #menu-icon {
            display: block;
            position: fixed;
            top: 20px;
            right: 20px;
            cursor: pointer;
            z-index: 2;
            font-size: 24px;
            color: #fff;
        }

        #notification-bell {
            position: absolute;
            top: 20px;
            right: 70px;
            cursor: pointer;
        }

        #notification-bell:before {
            content: "\1F514"; 
            font-size: 25px;
        }

        #notification-count {
            background-color: red;
            color: white;
            border-radius: 70%;
            padding: 5px 10px;
            position: absolute;
            top: -10px;
            right: -10px;
        }

        .login-wrapper {
            height: 80vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form {
            position: relative;
            width: 100%;
            max-width: 500px;
            padding: 60px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
        }

        .form h3 {
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 2rem;
            color: white;
        }

        .form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            outline: none;
            font-size: 16px;
        }

        .form input:focus {
            background: rgba(255, 255, 255, 0.3);
        }

        .form span {
            display: block;
            text-align: left;
            margin-top: -10px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #ff652f;
        }

        .form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #ff652f;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form button:hover {
            background-color: #e94e20;
        }
    </style>

</head>
<body>
    <header>HCAS Appointment - Change Password</header>


    <div id="notification-bell">
        <span id="notification-count">5</span>
    </div>

    <nav>
        <a href="doctor_home.php">Home</a>
        <a href="view_appointment.php">View Appointment</a>
        <a href="message.php">Next Appointment</a>
        <a href="add_availability.php">Add Availability</a>
        <a href="profile.php">My Profile</a>
    </nav>
	
    <div id="sidebar">
        <a href="logout.php">SignOut</a>
        <h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>
	
 <div class="login-wrapper">	
 <div class="form">
        <h3>Change Password</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
            <br><br>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required onkeyup="checkPasswordStrength(this.value)">
            <span id="password_strength"></span>
            <br><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <br><br>
            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>
</div>

<script>
        function checkPasswordStrength(password) {
            // Define the regular expressions for password strength
            let strongRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+}{":;\'?\/><.,|`~])(?=.*[^\w\d\s]).{8,}$/;
            let mediumRegex = /^(?=.*\d)(?=.*[a-zA-Z]).{6,}$/;

            // Check the password strength and display appropriate message
            if (strongRegex.test(password)) {
                document.getElementById("password_strength").innerHTML = '<span style="color: green;">Strong password</span>';
            } else if (mediumRegex.test(password)) {
                document.getElementById("password_strength").innerHTML = '<span style="color: orange;">Medium password</span>';
            } else {
                document.getElementById("password_strength").innerHTML = '<span style="color: red;">Weak password</span>';
            }
        }
    </script>
</body>
</html>
