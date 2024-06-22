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
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: patient_login.php");
    exit();
}

// Check if email is set in session
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// Fetch patient_id based on email
$patient_id = null;
if ($email) {
    $sql = "SELECT patient_id FROM patients WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        $patient_id = $patient['patient_id'];
    }
}

// Retrieve doctor and service details for display
$doctor_id = isset($_POST['doctor']) ? $_POST['doctor'] : null;
$service_id = isset($_POST['service']) ? $_POST['service'] : null;
$date = isset($_POST['date']) ? $_POST['date'] : null;

// Retrieve doctor's availability details
$start_time = $end_time = $start_time_ampm = $end_time_ampm = null;
if ($doctor_id && $service_id && $date) {
    $sql_doctor_availability = "SELECT da.start_time, da.end_time FROM doctor_availability da WHERE da.doctor_id = ? AND da.date = ? AND da.service_id = ?";
    $stmt_doctor_availability = $conn->prepare($sql_doctor_availability);
    $stmt_doctor_availability->bind_param("iss", $doctor_id, $date, $service_id);
    $stmt_doctor_availability->execute();
    $result_doctor_availability = $stmt_doctor_availability->get_result();
    $doctor_availability = $result_doctor_availability->fetch_assoc();
    if ($doctor_availability) {
        $start_time = $doctor_availability['start_time'];
        $end_time = $doctor_availability['end_time'];

        // Determine AM or PM for doctor's availability
        $start_time_parts = explode(':', $start_time);
        $end_time_parts = explode(':', $end_time);
        $start_time_ampm = ($start_time_parts[0] < 12) ? 'AM' : 'PM';
        $end_time_ampm = ($end_time_parts[0] < 12) ? 'AM' : 'PM';
    }
}

$doctor_name = $service_name = "";

if ($doctor_id) {
    $sql_doctor = "SELECT username, max_appointments_per_day FROM doctors WHERE doctor_id = ?";
    $stmt_doctor = $conn->prepare($sql_doctor);
    $stmt_doctor->bind_param("i", $doctor_id);
    $stmt_doctor->execute();
    $result_doctor = $stmt_doctor->get_result();
    $doctor = $result_doctor->fetch_assoc();
    $doctor_name = $doctor['username'];
    $max_appointments_per_day = $doctor['max_appointments_per_day'];
}

if ($service_id) {
    $sql_service = "SELECT name FROM services WHERE service_id = ?";
    $stmt_service = $conn->prepare($sql_service);
    $stmt_service->bind_param("i", $service_id);
    $stmt_service->execute();
    $result_service = $stmt_service->get_result();
    $service = $result_service->fetch_assoc();
    $service_name = $service['name'];
}

// Retrieve the last booked appointment time for the specific doctor
$sql_last_booking = "SELECT MAX(appointment_time) AS last_booking FROM appointment WHERE doctor_id = ?";
$stmt_last_booking = $conn->prepare($sql_last_booking);
$stmt_last_booking->bind_param("i", $doctor_id);
$stmt_last_booking->execute();
$result_last_booking = $stmt_last_booking->get_result();
$last_booking_row = $result_last_booking->fetch_assoc();
$last_booking_time = $last_booking_row['last_booking'];

// Calculate the next available time slot
if ($last_booking_time) {
    // There are existing appointments
    $next_available_time = strtotime($last_booking_time) + (15 * 60); // Add 15 minutes in seconds
} else {
    // No existing appointments, use doctor's availability schedule
    $next_available_time = strtotime("$date $start_time"); // Use the start time from doctor's availability
}

$next_available_time = date('Y-m-d H:i:s', $next_available_time); // Format the time

// Ensure that the calculated time falls within the doctor's availability schedule
if (strtotime($next_available_time) < strtotime("$date $start_time") || strtotime($next_available_time) > strtotime("$date $end_time")) {
    // Adjust the next available time based on doctor's availability
    $next_available_time = strtotime("$date $start_time"); // Use the start time from doctor's availability
    $next_available_time = date('Y-m-d H:i:s', $next_available_time); // Format the time
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form fields are set
    if (isset($_POST["appointment_time"]) && isset($_POST["appointment_date"]) && isset($_POST["doctor"]) && isset($_POST["service"]) && isset($_POST["phone"])) {
        // Get form data
        $appointment_date = $_POST["appointment_date"];
        $appointment_time = $_POST["appointment_time"];
        $doctor_id = $_POST["doctor"];
        $service_id = $_POST["service"];
        $phone = $_POST["phone"];

        // Combine date and time
        $appointment_datetime = $appointment_date . ' ' . $appointment_time;

        // Check if the patient already has an appointment on the same day
        $sql_check_patient_appointment = "SELECT COUNT(*) AS patient_appointments FROM appointment WHERE patient_id = ? AND DATE(appointment_time) = ?";
        $stmt_check_patient_appointment = $conn->prepare($sql_check_patient_appointment);
        $stmt_check_patient_appointment->bind_param("is", $patient_id, $appointment_date);
        $stmt_check_patient_appointment->execute();
        $result_check_patient_appointment = $stmt_check_patient_appointment->get_result();
        $row_check_patient_appointment = $result_check_patient_appointment->fetch_assoc();
        $patient_appointments_count = $row_check_patient_appointment['patient_appointments'];

        if ($patient_appointments_count > 0) {
            // The patient already has an appointment on this day
            echo "<script>alert('You already have an appointment on this date. Please select a different date.'); window.location.href = 'book_appointment.php';</script>";
        } else {
            // Check if the doctor has reached the max appointments per day
            $sql_check_max_appointments = "SELECT COUNT(*) AS daily_appointments FROM appointment WHERE doctor_id = ? AND DATE(appointment_time) = ?";
            $stmt_check_max_appointments = $conn->prepare($sql_check_max_appointments);
            $stmt_check_max_appointments->bind_param("is", $doctor_id, $appointment_date);
            $stmt_check_max_appointments->execute();
            $result_check_max_appointments = $stmt_check_max_appointments->get_result();
            $row_check_max_appointments = $result_check_max_appointments->fetch_assoc();
            $daily_appointments_count = $row_check_max_appointments['daily_appointments'];

            if ($daily_appointments_count >= $max_appointments_per_day) {
                // The doctor has reached the maximum appointments per day
                echo "<script>alert('The doctor has reached the maximum number of appointments for the day. Please select a different day.'); window.location.href = 'book_appointment.php';</script>";
            } else {
                // Check if the appointment slot is available
                $sql_check_availability = "SELECT COUNT(*) AS appointments_count FROM appointment WHERE doctor_id = ? AND (TIMESTAMPDIFF(MINUTE, appointment_time, ?) BETWEEN -10 AND 10)";
                $stmt_check_availability = $conn->prepare($sql_check_availability);
                $stmt_check_availability->bind_param("is", $doctor_id, $appointment_datetime);
                $stmt_check_availability->execute();
                $result_check_availability = $stmt_check_availability->get_result();
                $row_check_availability = $result_check_availability->fetch_assoc();
                $appointments_count = $row_check_availability['appointments_count'];

                if ($appointments_count > 0) {
                    // The appointment slot is not available
                    echo "<script>alert('This time slot is not available. Please select a different time.'); window.location.href = 'book_appointment.php';</script>";
                } else {
                    // Continue with appointment booking
                    // Insert appointment data into the database
                    $sql_insert_appointment = "INSERT INTO appointment (patient_id, doctor_id, service_id, appointment_time, phone, email) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert_appointment = $conn->prepare($sql_insert_appointment);
                    $stmt_insert_appointment->bind_param("iiisss", $patient_id, $doctor_id, $service_id, $appointment_datetime, $phone, $email);
                    $stmt_insert_appointment->execute();

                    // Check for errors
                    if ($stmt_insert_appointment->error) {
                        echo "Error: " . $stmt_insert_appointment->error;
                    } else {
                        // Redirect to booking success page
                        header("Location: booking_success.php");
                        exit();
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-J1Xj5oNsRt2jLXp8u+7j3Zt5OmuIO9+1eyQWOKb9vHQQZGzOvQ7tn2sAcG8g3EjlMIvc8aGozT4vxeBb0G/mDg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>HCAS - PATIENT CONFIRM APPOINTMENT</title>
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

        .form {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
            margin: 0 auto;
            margin-top: 50px;
        }
        .form h2 {
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 2rem;
        }

        .form .input-group {
            position: relative;
        }

        .form .input-group input,
        .form .input-group select {
            width: 100%;
            padding: 10px 0;
            font-size: 1rem;
            letter-spacing: 1px;
            margin-bottom: 30px;
            border: none;
            border-bottom: 1px solid #fff;
            outline: none;
            background-color: transparent;
            color: inherit;
        }

        .form .input-group label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px 0;
            font-size: 1rem;
            pointer-events: none;
            transition: 0.3s ease-out;
        }

        .form .input-group input:focus + label,
        .form .input-group input:valid + label {
            transform: translateY(-18px);
            color: #fff;
            font-size: 0.8rem;
        }

        .form .submit-btn {
            display: block;
            margin-left: auto;
            border: none;
            outline: none;
            background: #ff652f;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .doctor-info {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .doctor-info h2 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .doctor-info p {
            color: #fff;
            margin-bottom: 10px;
        }

        .doctor-info p span {
            font-weight: bold;
        }

        /* Additional CSS for responsive design */
        @media screen and (max-width: 600px) {
            .form {
                padding: 20px;
            }
        }
		 #left-content {
        position: fixed;
        top: 60%;
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
        HCAS - Patient Confirm Appointment
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

 <div class="form">
        <!-- Display selected doctor and service information -->
        <div class="doctor-info">
            <h2>Doctor Information</h2>
            <p><strong>Doctor:</strong> <?php echo htmlspecialchars($doctor_name); ?></p>
            <p><strong>Service:</strong> <?php echo htmlspecialchars($service_name); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($date); ?></p>
            <p><strong>Availability:</strong> <?php echo htmlspecialchars($start_time); ?> <?php echo $start_time_ampm; ?> - <?php echo htmlspecialchars($end_time); ?> <?php echo $end_time_ampm; ?></p>
        </div>

        <!-- Form to confirm appointment -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Confirm Appointment</h2>
            <div class="input-group">
                <input type="hidden" name="doctor" value="<?php echo htmlspecialchars($doctor_id); ?>">
                <input type="hidden" name="service" value="<?php echo htmlspecialchars($service_id); ?>">
                <input type="hidden" name="appointment_date" value="<?php echo htmlspecialchars($date); ?>">
            </div>

            <label for="appointment_time">Appointment Time<span style="color: red;">*</span></label>
            <div class="input-group">
                <input type="time" name="appointment_time" required>
            </div>

            <label for="phone">Contact Number<span style="color: red;">*</span></label>
            <div class="input-group">
                <input type="text" name="phone" required>
            </div>

            <label for="email">Email</label>
            <div class="input-group">
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>

            <input type="submit" class="submit-btn" value="Confirm Appointment">
        </form>
    </div>


    <script>
        function validateAppointmentTime() {
            var appointmentTime = document.getElementsByName('appointment_time')[0].value;
            var startTime = '<?php echo date("H:i", strtotime($start_time)); ?>'; // Use PHP to echo the start time in 24-hour format
            var endTime = '<?php echo date("H:i", strtotime($end_time)); ?>'; // Use PHP to echo the end time in 24-hour format

            // Convert appointmentTime, startTime, and endTime to time objects
            var appointmentTimeObj = new Date('1970-01-01T' + appointmentTime + ':00');
            var startTimeObj = new Date('1970-01-01T' + startTime + ':00');
            var endTimeObj = new Date('1970-01-01T' + endTime + ':00');

            // Calculate the difference between the appointment time and the end time of doctor's availability
            var timeDifference = (endTimeObj - appointmentTimeObj) / 1000 / 60; // in minutes

            // Check if appointment time is within doctor's availability
            if (appointmentTimeObj < startTimeObj || appointmentTimeObj > endTimeObj) {
                alert('Please set the appointment time within the doctor\'s availability.');
                return false;
            } else if (timeDifference <= 30) { // Check if the appointment time is within 30 minutes of the end time
                alert('The doctor\'s availability is ending soon. Please consider booking at an earlier time or on another day.');
                return false;
            }
            return true;
        }

        // Add event listener to form submission for validation
        document.querySelector('form').addEventListener('submit', function(event) {
            if (!validateAppointmentTime()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
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