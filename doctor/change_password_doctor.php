<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCTOR - Change Password</title>
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
            height: 65vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form {
            position: relative;
            width: 100%;
            max-width: 500px;
            padding: 50px 50px 50px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
        }

        .form::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: rgba(255, 255, 255, 0.08);
            transform: skewX(-26deg);
            transform-origin: bottom left;
            border-radius: 10px;
            pointer-events: none;
        }

        .form h2 {
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 2rem;
            color: white;
        }

        .form .input-group {
            position: relative;
        }

        .form select[name="appointment_id"] {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            letter-spacing: 1px;
            margin-bottom: 30px;
            border: none;
            border-radius: 5px;
            outline: none;
            background-color: #fff;
            color: #333;
        }

        .form select[name="appointment_id"] option {
            background-color: #fff;
            color: #333;
        }

        .input-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            cursor: pointer;
        }

        .submit-btn {
            width: 150px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #ff652f;
            cursor: pointer;
            color: #333;
            font-weight: bold;
        }

        .profile-section {
            margin-bottom: 20px;
        }

        .profile-section h3 {
            margin-bottom: 10px;
        }

        .profile-section input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
            <h2>Change Password</h2>
            <form action="change_password.php" method="post">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required><br><br>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required><br><br>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>

                <input class="submit-btn" type="submit" value="Change Password">
            </form>
        </div>
    </div>
</body>
</html>
