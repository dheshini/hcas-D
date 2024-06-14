<?php
session_start();
include '../config.php';
include('../session.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>HCAS - PATIENT APPOINTMENT SUCCESS</title>
	<style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Times;
            margin: 0;
            padding: 10;
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
			border-radius:4px;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .welcome-container {
            padding: 50px;
            text-align: center;
        }

        .welcome-container h2 {
            font-size: 30px;
            margin-bottom: 20px;
        }

        .welcome-container p {
            font-size: 20px;
            line-height: 1.6;
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
		
		#notification-bell{
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
    width: 100%;
    max-width: 500px;
    padding: 40px;
	background: rgba(200, 255, 39, 0.4); /* Lighter green with transparency */
    border-radius: 10px;
    color:black;
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
    margin: 0 auto;
    margin-top: 50px;
}

	 </style>
</head>
<body>
    <header>
        HCAS - Patient Booking Success
</header>

<div id="notification-bell">
       <span id="notification-count">5</span>
</div>

    <nav>
         <a href="user_home.php">Home</a>
        <a href="book_appointment.php">Book Appointment</a>
        <a href="booking_history.php">View Appointments</a>
        <a href="profile.php">Account</a>
		<a href="contact_us.php">Contact Us</a>
    </nav>

    <div id="sidebar">
        <a href="logout.php">SignOut<a>
		<h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>
	
    <div id="menu-icon">&#9776;</div>
	 <div class="login-wrapper">
	 
 <div class="form">
		<center><i class="fa fa-check-circle" style="font-size:150px;color:green"></i></center>
		<h1>Booking Successful</h1>
		<p>Your appointment has been successfully booked.</p>
		<p>Thank you for choosing our service.</p>
   </div>
	
	<script>
    // Function to redirect to the home page after a delay
    setTimeout(function() {
        window.location.href = "booking_history.php";
    }, 5000); // 5000 milliseconds = 5 seconds
</script>
</form>
	<script>
        document.getElementById('menu-icon').addEventListener('click', function () {
            document.getElementById('sidebar').style.width = (document.getElementById('sidebar').style.width === '250px') ? '0' : '250px';
        });
    </script>
	
		<script>
        // Function to update the news content with the current date and time
        function updateNews() {
            const currentDate = new Date();
            const formattedDate = currentDate.toLocaleString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric'
            });
            document.getElementById('news-content').innerHTML = `<p>${formattedDate}</p>`;
        }

        // Initial call to update the news content
        updateNews();

        // Function to update the news content every second
        setInterval(updateNews, 1000);
		
    </script>
</body>
</html>
