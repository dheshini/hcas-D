<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: patient_login.php");
    exit();
}
include '../config.php'; // Include the database connection script
include('../session.php'); // Include the session script

// Fetch the latest news
$sql = "SELECT * FROM latest_news ORDER BY updated_at DESC LIMIT 1";
$result = $conn->query($sql);
$news = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-J1Xj5oNsRt2jLXp8u+7j3Zt5OmuIO9+1eyQWOKb9vHQQZGzOvQ7tn2sAcG8g3EjlMIvc8aGozT4vxeBb0G/mDg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>HCAS - PATIENT DASHBOARD</title>
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
    background-color: rgba(255, 255, 255, 0.1);
}
#sidebar h3 {
    color: #fff;
    padding: 10px 20px;
    border-bottom: 1px solid #555; /* Add separator */
}


#news-content {
    padding: 20px;
    color: #fff;
}

#news-content h4 {
    font-size: 20px;
    margin-bottom: 10px;
}

#news-content p {
    font-size: 16px;
    line-height: 1.5;
}

#news-content small {
    font-size: 12px;
    color: #bbb;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #sidebar {
        width: 200px; /* Adjust width for smaller screens */
        padding-top: 50px;
    }
}

@media (max-width: 576px) {
    #sidebar {
        width: 150px; /* Further adjust width for very small screens */
    }
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
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }

              .content-wrapper {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
 #left-content {
        position: fixed;
        top:60%;
        left: 20px;
        transform: translateY(-50%);
        background-color: #f9f9f9;
        color: #333;
        padding: 30px;
        width: 300px;
		height: 900px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #left-content h3 {
        margin-top: 0;
        font-size: 1.5em;
        color: #333;
        display: flex;
        align-items: center;
    }

    #left-content h3::before {
        content: "⚠️";
        margin-right: 10px;
        font-size: 1.2em;
    }

    #left-content p {
        font-size: 1.2em;
        color: #555;
    }
	   #left-content ul {
        margin-top: 10px; /* Add some space above the list */
        padding-left: 20px; /* Indent the list items */
    }

        .video-section,
        .gif-section,
        .tiktok-section {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .video-section h3,
        .gif-section h3,
        .tiktok-section h3 {
            color: #fff;
            margin-bottom: 20px;
        }

        .video-section iframe,
        .tiktok-section iframe {
            width: 100%;
            height: 400px;
        }

   /* TikTok video container */
        .tiktok-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .tiktok-embed {
            max-width: 800px;
            min-width: 325px;
        }

        .tiktok-embed section {
            margin-bottom: 20px;
        }

        .tiktok-embed section p,
        .tiktok-embed section a {
            color: #fff;
        }

        .tiktok-embed section a {
            text-decoration: none;
        }

    </style>
</head>

<body>
    <header>
        HCAS - Patient Dashboard
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


    <div class="welcome-container">
        <?php if (isset($_SESSION['email'])) : ?>
            <h2>Welcome <?php echo $_SESSION['email']; ?></h2>
            <p>Manage your appointments and stay connected with our clinic.</p>
        <?php else : ?>
            <p>Error: Email not found.</p>
        <?php endif; ?>
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

	
<div id="left-content">
    <h3>DR HANNANI CINIC OPENS</h3>
    <h4>We are pleased to announce that Dr. Hannani Clinic is open every day;</h4>
	<ul>
        <li>Monday : 9.00am – 10.00pm</li>
        <li>Tuesday : 9.00am – 10.00pm</li>
        <li>Wednesday : 9.00am – 10.00pm</li>
        <li>Thursday : 9.00am – 10.00pm</li>
        <li>Friday : 9.00am – 10.00pm</li>
        <li>Saturday : 9.00am – 10.00pm</li>
        <strong><li>Sunday : CLOSED </li></strong>
    </ul>	
		 <h3>Contact Us</h3>
		<h4>For appointments and inquiries, please contact us at :</h4>
    <ul>
        <li>Phone : 07-12345678</li>
        <li>Email : clinicdrhannani@gmail.com</li>
        <li>Address : Klinik Dr Hannani (Batu Pahat),Jalan Universiti 4,<br>86400 Parit Raja,Johor </li>
    </ul>
		  <h3>Services Offered</h3>
		<h4>Our clinic offers a wide range of medical services, including but not limited to :</h4>
    <ul>
        <li>General consultations</li>
        <li>Preventive health check-ups.<br> More..</li>
    </ul>
</div>
      <!-- TikTok Videos -->
    <div class="video-section">
        <h3>TikTok Videos</h3>
        <div class="tiktok-container">
            <blockquote class="tiktok-embed" cite="https://www.tiktok.com/@klinikdrhannanijb/video/7341348938932374785" data-video-id="7341348938932374785">
                <section>
                    <div class="tiktok-video" data-id="7341348938932374785"></div>
                </section>
            </blockquote>
            <blockquote class="tiktok-embed" cite="https://www.tiktok.com/@klinikdrhannanijb/video/7371009208038214929" data-video-id="7371009208038214929">
                <section>
                    <div class="tiktok-video" data-id="7371009208038214929"></div>
                </section>
            </blockquote>
            <!-- Add more TikTok videos as needed -->
        </div>
    </div>

    <div class="video-section">
        <h3>Featured Video</h3>
        <iframe src="https://www.youtube.com/embed/nRxpNxngSew" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
	  <script src="https://www.tiktok.com/embed.js" async></script>
	  
	  
	  
</body>

</html>