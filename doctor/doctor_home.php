<?php session_start();
include '../config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-7NbQdEEc5cAmwYz20T25Yt72iFpSnh/6yx36eeIwGXANL4/rJzD9O0q6spQgRd1YTDthL+m0cGd1CKnLBp4cTQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>DOCTOR - Dashboard</title>
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
h1{
  text-align: center;
  letter-spacing: 1px;
  margin-bottom: 2rem;
}
  
.resource-section,
        .video-section,
        .news-section {
            background-color: rgba(255, 255, 255, 0.8);
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .resource-section h3,
        .video-section h3,
        .news-section h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .resource-section ul,
        .news-section ul {
            list-style-type: none;
            padding: 0;
        }

        .resource-section ul li,
        .news-section ul li {
            margin-bottom: 5px;
        }

        .video-section iframe {
            width: 100%;
            height: 400px;
        }
.left-content {
    float: left;
    width: 20%; /* Adjust the width as needed */
    background-color: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    margin-right: 20px;
}

.left-content .section {
    margin-bottom: 20px;
}

.left-content h3 {
    color: #333;
    margin-bottom: 10px;
}

.left-content ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.left-content ul li {
    margin-bottom: 5px;
}

.left-content i {
    margin-right: 10px;
}

.left-content p {
    margin: 0;
}
.section .gif-container img {
    max-width: 100%; /* Ensure the images don't exceed the container width */
    height: auto; /* Maintain aspect ratio */
    margin-bottom: 10px; /* Add some space between the images */
}
	 </style>
</head>

<body>

    <header>
        HCAS Appointment - Doctor Dashboard
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

    <div id="sidebar">
        <a href="logout.php">SignOut<a>
		<h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>
    </div>
<div class="left-content">
<div class="section">
        <h3><i class="fas fa-newspaper"></i> Healthcare News and Updates (Malaysia)</h3>
        <p>Stay updated with the latest healthcare news and updates relevant to Malaysia. We provide timely information on healthcare policies, medical breakthroughs, public health announcements, and more to keep you informed and prepared.</p>
        <!-- Add links or sources for healthcare news and updates -->
    </div>

    <!-- Medical Resources section -->
    <div class="section">
        <h3><i class="fas fa-book-medical"></i> Medical Resources</h3>
        <p>Access a variety of medical resources to support your practice and enhance patient care. From medical journals and research articles to clinical guidelines and educational materials, our platform offers a comprehensive collection of resources tailored to your professional needs.</p>
        <!-- Add links or sources for medical resources -->
    </div>
    <!-- Doctor's Working Hours or Quotes section -->
    <div class="section">
        <h3><i class="fas fa-clock"></i> Doctor's Working Hours</h3>
        <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
        <p>Saturday: 9:00 AM - 12:00 PM</p>
        <!-- Add more working hours or quotes as needed -->
    </div>

<!-- GIF Quotes section -->
<div class="section">
    <h3><i class="fas fa-image"></i>Motivational Quotes</h3>
    <div class="gif-container">
        <img src="../image/quote1.jpg" alt="GIF Quote 1">
        <img src="../image/quote2.jpg" alt="GIF Quote 2">
    </div>
</div>
</div>
<div class="welcome-container">
    <?php if(isset($_SESSION['email'])) : ?>
        <h2>Hi Dr, <?php echo $_SESSION['email']; ?></h2>
        <p>This is your personalized dashboard. From here, you can manage your appointments, </p>
		<p>update your profile, and view patient details</p>
	<?php else : ?>
        <p>Error: Email not found.</p>
    <?php endif; ?>
	</div>



<div class="video-section">
    <h3>YouTube Video</h3>
    <iframe width="560" height="315" src="https://www.youtube.com/embed/-iezOqTIIB4" frameborder="0" allowfullscreen></iframe>
</div>


  <div class="resource-section">
        <h3><i class="fas fa-book"></i> Educational Resources</h3>
        <ul>
            <li><i class="far fa-newspaper"></i> <a href="https://mjphm.org/index.php/mjphm">Medical Journals</a></li>
            <li><i class="fas fa-laptop"></i> <a href="https://post-grad.hms.harvard.edu/leadership-medicine-southeast-asia-2025?utm_term=health%20care%20professional%20training&utm_campaign=lim2025&utm_source=google&utm_medium=cpc&gad_source=1">Online Courses</a></li>
        </ul>
    </div>

    <div class="resource-section">
        <h3><i class="fas fa-heartbeat"></i> Quick Links about Health</h3>
        <ul>
            <li><i class="fas fa-user-md"></i> <a href="https://ldh.la.gov/page/respiratory-home">Covid-19</a></li>
            <li><i class="fas fa-dumbbell"></i> <a href="https://ldh.la.gov/subhome/13">Fitness</a></li>
            <li><i class="fas fa-brain"></i> <a href="https://ldh.la.gov/page/mental-health-services">Mental Health</a></li>
            <li><i class="fas fa-baby"></i> <a href="https://ldh.la.gov/subhome/29">Pregnancy Info</a></li>
        
        </ul>
    </div>

    <div class="news-section">
        <h3><i class="fas fa-newspaper"></i> Medical News</h3>
        <ul>
            <li><i class="fas fa-flask"></i> <a href="https://www.mmgazette.com/">Latest Research Findings</a></li>
            <li><i class="fas fa-vials"></i> <a href="http://health.bernama.com/">Latest Research Findings in Bernama</a></li>
            <li><i class="fas fa-balance-scale"></i> <a href="https://www.malaysia.gov.my/portal/content/30694">Healthcare Policy Updates</a></li>
        </ul>
    </div>

 <div id="menu-icon">&#9776;</div>
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