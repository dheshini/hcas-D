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

// Retrieve appointments for the current doctor along with patient's phone number
if (!is_null($doctor_id)) {
    $sql = "SELECT a.*, p.phone AS patient_phone, a.status FROM appointment a 
            JOIN patients p ON a.patient_id = p.patient_id 
            WHERE a.doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCTOR - View Patient Appointment</title>
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
            height: 80vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form {
            position: relative;
            width: 150%;
            max-width: 1300px;
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

        .form .input-group input {
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
            color: white;
            font-size: 0.8rem;
        }

        .input-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            cursor: pointer;
        }

     
        table {
            border-collapse: collapse;
            width: 102%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: grey;
			text-align:center;
        }

		  .submit-btn {
           width: 140px; /* Set width of the dropdown */
            padding: 8px; /* Add padding */
            border: 1px solid #ccc; /* Add border */
            border-radius: 5px; /* Add border radius */
            background-color:  #ff652f; /* Set background color */
            cursor: pointer; /* Set cursor style */
            color: #333; /* Set text color */
			font-weight:bold;
        }
		
form select[name="status"] {
    width: 150px; 
    padding: 8px; 
    border: 1px solid #ccc; 
    border-radius: 5px; 
    background-color:  #ff652f; 
    cursor: pointer; 
    color: #333;
    font-weight: bold;
}

form select[name="status"] option[value="Approved"] {
    background-color: #5cb85c; 
}

/* Style the Pending option */
form select[name="status"] option[value="Pending"] {
    background-color: yellow;
}

form select[name="status"] option[value="Cancelled"] {
    background-color: red;
}


	 </style>
</head>
<body>
    <header>
        HCAS Appointment - View Patient Appointment
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
            <h2>Your Appointments</h2>
            <form id="appointmentForm" action="view_patient_record.php" method="post">
                <table>
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Appointment ID</th>
                            <th>Patient ID</th>
                            <th>Service ID</th>
                            <th>Appointment Time</th>
                            <th>Patient Phone Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <!-- Add a radio button for selecting the patient ID -->
                                    <input type="radio" name="selected_patient_id" value="<?php echo $row['patient_id']; ?>">
                                </td>
                                <td><?php echo $row['appointment_id']; ?></td>
                                <td><?php echo $row['patient_id']; ?></td>
                                <td><?php echo $row['service_id']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['patient_phone']; ?></td>
                                <td style="color: <?php echo ($row['status'] == 'Approved') ? 'green' : 'inherit'; ?>"><?php echo $row['status']; ?></td>
                                <td>
                                    <form action="update_status.php" method="post" id="updateForm_<?php echo $row['appointment_id']; ?>">
                                        <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                        <select name="status">
                                            <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="Approved" <?php if ($row['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                                            <option value="Cancelled" <?php if ($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="submit-btn">Update Status</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <br><br>
                <center>
                    <!-- Change the onclick function to checkSelectedPatient() -->
                    <button type="button" class="submit-btn" onclick="checkSelection()">View Patient Record</button>
                </center>
            </form>
        </div>
    </div>

    <div id="menu-icon">&#9776;</div>
    <script>
        function checkSelection() {
            const selectedPatient = document.querySelector('input[name="selected_patient_id"]:checked');
            if (!selectedPatient) {
                alert('Please select a patient');
            } else {
                document.getElementById('appointmentForm').submit();
            }
        }

        document.getElementById('menu-icon').addEventListener('click', function () {
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
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>