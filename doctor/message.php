<?php
session_start();

// Include config file
include '../config.php';

// Retrieve doctor ID from session
$doctor_id = null;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT doctor_id FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $doctor_id = $row['doctor_id'];
    }
    $stmt->close();
}

// Retrieve appointments for the next day for the current doctor
if (!is_null($doctor_id)) {
    // Get the current date
    $currentDate = date('Y-m-d');

    // Calculate the next day
    $nextDay = date('Y-m-d', strtotime($currentDate . ' +1 day'));

    // Query appointments for the next day
    $sql = "SELECT * FROM appointment WHERE doctor_id = ? AND DATE(appointment_time) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctor_id, $nextDay);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCTOR - Send Message</title>
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
			border-radius:4px;
        }

        nav a:hover {
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

        /* Style the select dropdown */
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

        /* Style the options within the select dropdown */
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
           width: 150px; /* Set width of the dropdown */
            padding: 8px; /* Add padding */
            border: 1px solid #ccc; /* Add border */
            border-radius: 5px; /* Add border radius */
            background-color:  #ff652f; /* Set background color */
            cursor: pointer; /* Set cursor style */
            color: #333; /* Set text color */
			font-weight:bold;
        }

	 </style>
</head>
<body>
    <header>
        HCAS Appointment - Send Message
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

 <div class="login-wrapper">
        <div class="form">
            <h2>Appointments for Tomorrow</h2>
            <?php if ($result->num_rows > 0) : ?>
                <form action="send_message_action.php" method="post">
                    <!-- Style the select dropdown -->
                    <select name="appointment_id">
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <option value="<?php echo $row['appointment_id']; ?>">
                                <?php echo $row['appointment_time'] . ' - Patient ID: ' . $row['patient_id']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button class="submit-btn" type="submit">Send Message</button>
                </form>
            <?php else : ?>
                <p>No appointments scheduled for tomorrow.</p>
            <?php endif; ?>
        </div>
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
