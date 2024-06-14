<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If the user is not logged in, redirect to the login page
    header("location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCAS - PASSWORD CHANGE SUCCESS </title>
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
		.profile-buttons {
            text-align: center;
            margin: 20px 0;
        }

        .profile-buttons a {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .profile-buttons a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
		
        /* Sidebar Styles */
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
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }
      .success-message {
            width: 100%;
            max-width: 500px;
            padding: 50px 50px 100px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .success-message h2 {
            margin-bottom: 20px;
        }

        .success-message a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff652f;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .success-message a:hover {
            background-color: #e94e20;
        }
    </style>
</head>
<body>

    <header>
        HCAS Appointment - Password Success Changed
    </header>

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
	
    <div class="profile-buttons">
        <a href="change_password.php">Change Password</a>  
		</div>
	
    <div id="sidebar">
        <a href="logout.php">SignOut</a>
        <h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>
	
   <div class="login-wrapper">
    <div class="success-message">
        <h2>Password Changed Successfully</h2>
        <p>Your password has been updated successfully.</p>
        <a href="profile.php">Go to Profile</a>
        <a href="logout.php" style="margin-left: 10px;">Logout</a>
    </div>
	</div>
</body>
</html>
