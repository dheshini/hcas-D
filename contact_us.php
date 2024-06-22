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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>HCAS - CONTACT US</title>
  <style>
        /* Your existing CSS styles */
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

        .form {
            position: relative;
            width: 100%;
            max-width: 500px;
            padding: 40px 40px 40px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
        }

        /* Additional styles for the contact section */
        #contact .container {
            font-weight: 400;
            padding: 20px;
            text-align: center;
        }

        #contact h2 {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        #contact h3 {
            font-size: 24px;
            margin-top: 30px;
        }

        #contact p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        #contact a {
            text-decoration: none;
            transition: color 0.3s ease;
        }

        /* Additional styles for responsiveness */
        @media only screen and (max-width: 768px) {
            .container {
                padding: 50px;
            }

            h2 {
                font-size: 28px;
            }

            h3 {
                font-size: 20px;
            }

            p {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <header>
        HCAS - Contact Us
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


<div class="login-wrapper">
        <center><section id="contact" class="form">
            <div class="container">
                <h3>CONTACT US</h3>
                <p>Need Assistance? Contact Us or Visit Us to Get the Best Medical Assistance.</p>    
                <p>Get the best service from our customer-friendly staff</p>
                <p>+60133403463</p>

                <h3>OUR PANEL</h3>
                <p>Email us the details of your company and we will handle it in detail</p>
                <p><i class="fas fa-envelope"></i> clinicdrhannani@gmail.com</p>
                <p><i class="fab fa-facebook-f"></i> <a href="https://web.facebook.com/rumahsihatkita/?_rdc=1&_rdr">Facebook</a> | <i class="fab fa-instagram"></i> <a href="https://www.instagram.com/drhannaniofficial/">Instagram</a> | <i class="fas fa-map-marker-alt"></i> <a href="https://maps.google.com/">Google Maps</a> | <br><i class="fab fa-whatsapp"></i><a href="https://wa.me/+6011-11146463">WhatsApp</a></p>
                <p><i class="fas fa-phone-alt"></i> 013-340 3463</p>

		   <h3>VISIT US</h3>
				<p>Visit our clinic to get the best and comfortable medical advice.</p>
				<p>Dr. Hannani Clinic</p>

				<h3>APPOINTMENT</h3>
				<p>Rumah Sihat Kita Sdn Bhd (1361050-X) aims to create a new perception of a clinic as if it were at home.</p>
				<p><i class="fab fa-facebook-f"></i> <a href="https://web.facebook.com/rumahsihatkita/?_rdc=1&_rdr">Facebook</a> | <i class="fab fa-instagram"></i> <a href="https://www.instagram.com/drhannaniofficial/">Instagram</a> | <i class="fas fa-map-marker-alt"></i> <a href="https://maps.google.com/">Google Maps</a> | <i class="fab fa-whatsapp"></i> <a href="https://wa.me/+6011-11146463">WhatsApp</a></p>
			</div>
		</section></center>
	</div>

	</body>
</html>

<?php
// Close connection
$conn->close();
?>
