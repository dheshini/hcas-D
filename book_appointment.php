<?php
session_start();
include '../config.php'; // Ensure this file contains your database connection details
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
// Retrieve list of services available on the selected date
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["date"])) {
    // Retrieve date from GET parameter
    $date = $_GET['date'];

    // Query to fetch available services for the given date
    $sql_available_services = "SELECT s.service_id, s.name, s.description
                               FROM services s
                               WHERE EXISTS (
                                   SELECT 1
                                   FROM doctor_availability da
                                   WHERE da.service_id = s.service_id
                                   AND da.date = ?
                               )";

    $stmt_available_services = $conn->prepare($sql_available_services);
    if (!$stmt_available_services) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt_available_services->bind_param("s", $date);
    $stmt_available_services->execute();
    $result_available_services = $stmt_available_services->get_result();
}

// Calculate current date and maximum future date
$current_date = date("Y-m-d");
$max_date = date("Y-m-d", strtotime("+6 months")); // Set maximum future date to 6 months from today

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-J1Xj5oNsRt2jLXp8u+7j3Zt5OmuIO9+1eyQWOKb9vHQQZGzOvQ7tn2sAcG8g3EjlMIvc8aGozT4vxeBb0G/mDg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    		    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<title>HCAS - PATIENT BOOKING</title>
       <style>
        /* Reset and General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../image/color1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
        }

        header {
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 36px;
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
            color: #fff;
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

        /* Page Content Styles */
        .content-wrapper {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date-selection {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            text-align: center;
        }

        .date-selection form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date-selection label {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .date-selection input[type="date"] {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 1rem;
            border: none;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .date-selection button {
            background: #ff652f;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1rem;
        }

        .service-list {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
        }

        .service-list h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .service-list ul {
            list-style: none;
            padding: 0;
        }

/* Style for list items displaying doctor information */
.service-list ul li {
    background: rgba(0, 0, 0, 0.5);
    margin: 10px 0;
    padding: 20px;
    border-radius: 5px;
    font-size: 1.2rem;
}

/* Style for list items when maximum appointments are reached */
.service-list ul li.booked {
    background: rgba(255, 0, 0, 0.5); /* Red background for fully booked */
}

/* Style for list items when appointments are available */
.service-list ul li.available {
    background: rgba(0, 128, 0, 0.5); /* Green background for available */
}


        .service-list ul ul li {
            font-size: 1rem;
            margin: 5px 0;
            padding: 10px;
        }

        .book-btn {
            background: #ff652f;
            color: #fff;
            border: none;
            padding: 5px 10px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                font-size: 24px;
            }

            .date-selection,
            .service-list {
                width: 100%;
                padding: 15px;
            }

            .service-list ul ul li {
                font-size: 0.9rem;
            }

            #sidebar {
                padding-top: 20px;
            }

            #menu-icon {
                font-size: 20px;
            }
        }
 #left-content {
        position: fixed;
        top: 50%;
        left: 20px;
        transform: translateY(-50%);
        background-color: #f9f9f9;
        color: #333;
        padding: 25px;
        width: 300px;
		height: 600px;
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

    /* Styling for list items */
    #left-content li {
        list-style-type: disc; /* Use a disc bullet point */
        margin-bottom: 8px; /* Space between list items */
        font-size: 1.1em; /* Font size for list items */
        color: #333; /* Text color */
    }
    </style>
</head>
<body>
    <header>
        HCAS - Patient View Available Doctors
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

        <div class="date-selection">
            <h1>Available Services and Doctors</h1>

            <!-- Form to select date to view available services -->
            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" required min="<?php echo $current_date; ?>"
                    max="<?php echo $max_date; ?>">
                <button type="submit">View Available Services</button>
            </form>
        </div>

        <!-- Display available services -->
<div class="service-list">
    <?php if (isset($result_available_services) && $result_available_services->num_rows > 0): ?>
        <h2>Available Services on <?php echo $date; ?>:</h2>
        <ul>
            <?php while ($row_service = $result_available_services->fetch_assoc()): ?>
                <li>
                    <strong><?php echo $row_service["name"]; ?></strong>
                    <p> - <?php echo $row_service["description"]; ?></p> <!-- Display service description -->
                    <ul>
                        <!-- Retrieve available doctors for the service on the selected date -->
                        <?php
                        $service_id = $row_service["service_id"];
                        $sql_available_doctors = "SELECT d.doctor_id, d.username, da.start_time, da.end_time, d.max_appointments_per_day
                                                  FROM doctor_availability da
                                                  INNER JOIN doctors d ON da.doctor_id = d.doctor_id
                                                  WHERE da.service_id = ? AND da.date = ?";
                        $stmt_available_doctors = $conn->prepare($sql_available_doctors);
                        if (!$stmt_available_doctors) {
                            die("Error preparing statement: " . $conn->error);
                        }
                        $stmt_available_doctors->bind_param("is", $service_id, $date);
                        $stmt_available_doctors->execute();
                        $result_available_doctors = $stmt_available_doctors->get_result();
                        ?>
                        <?php while ($row_doctor = $result_available_doctors->fetch_assoc()): ?>
                            <?php
                            // Count existing appointments for the current doctor on the selected date
                            $doctor_id = $row_doctor['doctor_id'];
                            $sql_count_appointments = "SELECT COUNT(*) AS appointments_count FROM appointment WHERE doctor_id = ? AND DATE(appointment_time) = ?";
                            $stmt_count_appointments = $conn->prepare($sql_count_appointments);
                            $stmt_count_appointments->bind_param("is", $doctor_id, $date);
                            $stmt_count_appointments->execute();
                            $result_count_appointments = $stmt_count_appointments->get_result();
                            $row_count_appointments = $result_count_appointments->fetch_assoc();
                            $appointments_count = $row_count_appointments['appointments_count'];
                            ?>

                            <li class="<?php echo ($appointments_count < $row_doctor['max_appointments_per_day']) ? 'available' : 'booked'; ?>">
                                    Doctor: <?php echo $row_doctor["username"]; ?> <!-- Display doctor's username -->
                                ( Time: <?php echo $row_doctor["start_time"]; ?> - <?php echo $row_doctor["end_time"]; ?> )
                                <?php if ($appointments_count < $row_doctor['max_appointments_per_day']): ?>
                                    <form method="post" action="booking.php">
                                        <input type="hidden" name="doctor" value="<?php echo $row_doctor["doctor_id"]; ?>">
                                        <input type="hidden" name="service" value="<?php echo $service_id; ?>">
                                        <input type="hidden" name="date" value="<?php echo $date; ?>">
                                        <br><input type="submit" value="Book" class="book-btn">
                                    </form>
                                <?php else: ?>
                                    <p style="color: yellow;">Maximum appointments reached for today</p>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["date"])): ?>
        <p>No services available on the selected date.</p>
    <?php endif; ?>
</div>

    <script>
        function validateDate() {
            const selectedDate = new Date(document.getElementById('date').value);
            const currentDate = new Date();
            const currentHour = currentDate.getHours();
            const currentMinute = currentDate.getMinutes();
            
            // Reset time to midnight for accurate comparison
            currentDate.setHours(0, 0, 0, 0);

            // If current time is past 12:00 PM, disallow booking for the current date
            if (currentHour > 12 || (currentHour === 12 && currentMinute > 0)) {
                if (selectedDate.toDateString() === currentDate.toDateString()) {
                    alert('Appointments cannot be made for the current date after 12:00 PM.');
                    document.getElementById('date').value = '';
                    return;
                }
            }

            // If selected date is before the current date, disallow booking
            if (selectedDate < currentDate) {
                alert('Please select a future date.');
                document.getElementById('date').value = '';
            }
        }
    </script>
    
<div id="left-content">
    <h3>Important Information</h3>
    <p>Please note that you are allowed to book only one appointment per day. Thank you for your understanding and cooperation.</p>
	<br>
	<br>
	  <h3>Appointment Preparation Tips</h3>
    <p>Before your appointment:</p>
    <ul>
        <li>Check your email for confirmation that your appointment has been approved by the doctor.</li>
        <li>Upon arrival at the clinic, our staff will guide you to the appropriate consultation room based on your appointment.</li>
    </ul>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>