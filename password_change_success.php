<?php
session_start();
include '../config.php';
include('../session.php');
// Fetch the latest news
$sql = "SELECT * FROM latest_news ORDER BY updated_at DESC LIMIT 1";
$result = $conn->query($sql);
$news = $result->fetch_assoc();
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: patient_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>HCAS - PATIENT PASSWORD CHANGES SUCCESS</title>
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
            border-radius: 4px;
        }

       nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        nav i {
            color: #ff652f;
            margin-right: 8px;
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
  height: 100vh;
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

.form h2 {
    text-align: center;
    letter-spacing: 1px;
    margin-bottom: 2rem;
    color: black; /* Dark green for the heading */
}


.submit-btn {
  display: block;
  margin-left: auto;
  border: none;
  outline: none;
  background: #ff652f;
  font-size: 16px;
  text-transform: uppercase;
  letter-spacing: 1px;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}
    </style>
</head>
<body>

    <header>
        HCAS - Patient Change Password Success
    </header>

    <div id="notification-bell">
        <span id="notification-count">5</span>
    </div>

   <nav>
        <a href="user_home.php"><i class="fas fa-home"></i>Home</a>
        <a href="book_appointment.php"><i class="fas fa-calendar-plus"></i>Book Appointment</a>
        <a href="booking_history.php"><i class="fas fa-history"></i>View Appointments</a>
        <a href="view_medical_records.php"><i class="fas fa-file-medical"></i>View Medical Records</a>
        <a href="profile.php"><i class="fas fa-user"></i>Account</a>
        <a href="contact_us.php"><i class="fas fa-envelope"></i>Contact Us</a>
    <hr>
	</nav>
	
 <nav>
        <a href="profile.php"><i class="fas fa-user"></i>Account</a>
        <a href="settings.php"><i class="fas fa-cog"></i>Settings</a>
        <a href="address.php"><i class="fas fa-map-marker-alt"></i>Address</a>
        <a href="medical_reports.php"><i class="fas fa-file-medical-alt"></i>Medical Reports</a>
        <a href="inbox.php"><i class="fas fa-inbox"></i>Inbox</a>
    </nav>
	

      <div id="sidebar">
        <a href="logout.php">SignOut<a>
        <h3>Today's News or Updates</h3>
        <div id="news-content">
            <?php if ($news): ?>
                <h4><?php echo $news['type']; ?></h4>
                <p><?php echo $news['content']; ?></p>
                <small>Last updated: <?php echo $news['updated_at']; ?></small>
            <?php else: ?>
                <p>No news available.</p>
            <?php endif; ?>
        </div>
    </div>
	  <div id="menu-icon">&#9776;</div>
    <script>
        document.getElementById('menu-icon').addEventListener('click', function() {
            document.getElementById('sidebar').style.width = (document.getElementById('sidebar').style.width === '250px') ? '0' : '250px';
        });

        // Session timeout logic
        const sessionTimeout = 300000; // 5 minutes in milliseconds
        const warningTime = 60000; // 1 minute in milliseconds
    </script>
	
	 <div class="form">
	<center><i class="fa fa-check-circle" style="font-size:150px;color:green"></i></center>
        <h2>Password Change Success</h2>
        <p>Your password has been successfully changed.</p>
        <p>You can now <a href="patient_login.php">login here</a> with your new password.</p>
    </div>


</body>
</html>