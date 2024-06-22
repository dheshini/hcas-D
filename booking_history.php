<?php
session_start();
include('../session.php');
include '../config.php'; // Ensure this file contains your database connection details

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

// Retrieve user's email from session
$email = $_SESSION['email'];

// Fetch patient_id based on email
$patient_id = null;
$sql = "SELECT patient_id FROM patients WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
    $patient_id = $patient['patient_id'];
} else {
    echo "Error: Patient ID not found for this email.";
    exit(); // Stop further execution if patient ID is not found
}

// Check if a cancel request was made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel']) && isset($_POST['appointment_id'])) {
        // Handle cancel request
        $appointment_id = $_POST['appointment_id'];

        // Delete the appointment from the database
        $delete_sql = "DELETE FROM appointment WHERE appointment_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $appointment_id);
        $delete_stmt->execute();

        // Redirect to booking_history.php
        header("Location: booking_history.php");
        exit();
    }
}

// Retrieve appointments for the current user
$sql = "SELECT a.appointment_id, a.appointment_time, d.username AS doctor_name, s.name AS service_name, a.status 
        FROM appointment a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN services s ON a.service_id = s.service_id
        WHERE a.patient_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    <title>HCAS - Patient View Appointment</title>
    <title>HCAS - PATIENT VIEW APPOINTMENT HISTORY</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        /* Form Styles */
        .form {
            width: 100%;
            max-width: 800px;
            padding: 40px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
            margin: 0 auto;
            margin-top: 50px;
        }

        .form  h1 {
            text-align: center;
            color: white;
            font-size: 2rem;
            margin-top: 20px;
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
        .form .input-group input:valid
 + label {
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

        .form .input-group select {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid #fff;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
        }

        .form .input-group select option {
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
        }

        .form ul {
            list-style: none;
            padding: 0;
        }

        .form ul li {
            background: rgba(0, 0, 0, 0.5);
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Responsive Table Styles */
        .table-wrapper {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #000;
        }
    </style>
</head>

<body>
    <header>
        HCAS - Patient View Appointment History
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
    <!-- Form Container -->
     <div class="form">
        <h1>Your Appointments</h1>
        <?php
        // Check if there are any appointments
        if ($result->num_rows === 0) {
            echo "<p>You don't have any appointments scheduled.</p>";
        } else {
            // Inform the patient about rescheduling if needed
            echo "<div class='reschedule-info'>";
            echo "<p><i class='fa fa-exclamation-circle' style='font-size:17px;color:red'></i> 
            If you want to reschedule an appointment, please select the appointment below and then click the 'Cancel Appointment' button. You'll then be directed to the new booking page.</p>";
            echo "</div>";
            ?>
            <form method="post">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Date</th>
                                <th>Doctor</th>
                                <th>Service</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                              <tr>
                            <td><input type="radio" name="appointment_id" value="<?php echo $row['appointment_id']; ?>"></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($row['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div><br><br>
                <button type="submit" name="cancel" class="submit-btn">
                    <i class="fas fa-calendar-times"></i> Cancel Appointment</button>
            </form>
        <?php } ?>
    </div>

</body>
</html>