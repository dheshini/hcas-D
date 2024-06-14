<?php
// Start the session
session_start();
include '../config.php'; // Include the database connection script
include('../session.php');


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Slideshow container */
        .slideshow-container {
            max-width: 800px;
            position: relative;
            margin: auto;
        }

        /* The dots/bullets/indicators */
        .dot {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
        }

        .active {
            background-color: #717171;
        }

        /* Fading animation */
        .fade {
            animation-name: fade;
            animation-duration: 1.5s;
        }

        .login-wrapper {
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #left-content {
            position: fixed;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            padding: 20px;
            width: 250px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        #right-content {
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            padding: 20px;
            width: 250px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1;
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
        
        // Session timeout logic
        const sessionTimeout = 300000; // 5 minutes in milliseconds
        const warningTime = 60000; // 1 minute in milliseconds
    </script>
	
    <div id="left-content">
        <h3>Anak Demam</h3>
        <p>Tips handle anak demam mengejut ni cek suhu pada bahagian dahi atau ketiak untuk mendapatkan bacaan suhu bayi. Jika lebih 38.5 ibu ayah kena bertindak</p>
        <br>
        <h3>Peramah & Berpengalaman</h3>
        <p>Doktor yang mendengar masalah dengan teliti dan memberi rawatan yang terbaik dengan sepenuh hati. Seperti anda kami juga mahukan yang terbaik.</p>
        <br>
                <h3>Working Hour</h3>
        <p>Monday to Saturday</p>
        <p>9am to 10pm</p>
        <p>Sunday(Close)</p>
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
