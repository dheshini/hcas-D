<?php
session_start();

// Include config file
include '../config.php';

// Retrieve appointment ID from the form submission
if (isset($_POST['selected_appointment_id'])) {
    $appointment_id = $_POST['selected_appointment_id'];

    // Query to retrieve patient details based on the appointment ID
    $sql = "SELECT * FROM patients WHERE patient_id = (SELECT patient_id FROM appointment WHERE appointment_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch patient details
    if ($row = $result->fetch_assoc()) {
        $patient_id = $row['patient_id'];

        // Fetch patient name from the database
        $sql_patient = "SELECT first_name, last_name FROM patients WHERE patient_id = ?";
        $stmt_patient = $conn->prepare($sql_patient);
        $stmt_patient->bind_param("i", $patient_id);
        $stmt_patient->execute();
        $result_patient = $stmt_patient->get_result();
        if ($row_patient = $result_patient->fetch_assoc()) {
            $patient_name = $row_patient['first_name'] . " " . $row_patient['last_name'];
        } else {
            $patient_name = 'Unknown';
        }

        // Fetch patient address from the database
        $sql_address = "SELECT * FROM addresses WHERE patient_id = ?";
        $stmt_address = $conn->prepare($sql_address);
        $stmt_address->bind_param("i", $patient_id);
        $stmt_address->execute();
        $result_address = $stmt_address->get_result();
        if ($row_address = $result_address->fetch_assoc()) {
            $home_address = $row_address['address_line1'];
            if (!empty($row_address['address_line2'])) {
                $home_address .= ', ' . $row_address['address_line2'];
            }
            $home_address .= ', ' . $row_address['city'] . ', ' . $row_address['state'] . ', ' . $row_address['postcode'];
        } else {
            $home_address = 'Unknown';
        }
    }
}
?>


        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>DOCTOR - Patient Record</title>
            <style>
             body{font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Times;
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
  position: relative;
  width: 100%;
  max-width: 500px;
  padding: 40px 40px 40px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 10px;
  color: #fff;
  box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
  font-family: sans-serif;
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
.submit-btn {
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
 .input-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            cursor: pointer;
        }

        /* Style the options in the dropdown */
        .input-group select option {
            padding: 5px;
        }

        table {
            border-collapse: collapse;
            width: 102%;
            margin-top: 20px;
			font-family: sans-serif;
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
            </style>
        </head>
        <body>
            <header>
                HCAS Appointment - Patient Record
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
<div class="login-wrapper">
    <div class="form" id="patientDetailsSection">
        <h2>Patient Details</h2>
        <table>
            <tr><td>Patient ID</td><td><?php echo $patient_id; ?></td></tr>
            <tr><td>Name</td><td><?php echo $patient_name; ?></td></tr>
            <tr><td>Email</td><td><?php echo $row['email']; ?></td></tr>
            <tr><td>NRIC/Passport</td><td><?php echo $row['nric_passport']; ?></td></tr>
            <tr><td>Phone</td><td><?php echo $row['phone']; ?></td></tr>
            <tr><td>Date of Birth</td><td><?php echo $row['date_of_birth']; ?></td></tr>
            <tr><td>Home Address</td><td><?php echo $home_address; ?></td></tr>
            <tr><td>Home Phone</td><td><?php echo $row['home_phone']; ?></td></tr>
            <tr><td>Office Phone</td><td><?php echo $row['office_phone']; ?></td></tr>
        </table>
        <br>
        <button class="submit-btn" onclick="showAddUpdateRecordSection()">Next</button>
    </div>

    <div class="form" id="addUpdateRecordSection" style="display: none;">
        <h2>Add / Update Patient Records</h2>
        <form id="recordForm" method="post" action="process_patient_record.php" onsubmit="displayAlert('Record submitted successfully');">
            <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">

            <!-- Input field for notes -->
            <label for="notes">Notes:</label><br>
            <div class="input-group">
                <textarea id="notes" name="notes" rows="4" cols="50"></textarea><br>
            </div>
            <br>

            <!-- Input field for diseases -->
            <label for="diseases">Diseases:</label><br>
            <div class="input-group">
                <textarea id="diseases" name="diseases" rows="4" cols="50"></textarea><br>
            </div>
            <br>

            <!-- Input field for additional medical records -->
            <label for="additional_medical_records">Additional Medical Records:</label><br>
            <div class="input-group">
                <textarea id="additional_medical_records" name="additional_medical_records" rows="4" cols="50"></textarea><br>
            </div>
            <br>

            <!-- Input field for record date -->
            <label for="record_date">Record Date:</label><br>
            <div class="input-group">
                <input type="datetime-local" id="record_date" name="record_date" value="<?php echo date('Y-m-d\TH:i'); ?>" readonly><br>
            </div>
            <br>
            <!-- Additional hidden fields -->
           <input type="hidden" name="doctor_id" value="<?php echo $_SESSION['doctor_id']; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
            <input type="hidden" name="record_id" value="<?php echo $record_id; ?>">

            <!-- Buttons -->
            <button class="submit-btn" onclick="goBack()">Back</button><br>
            <button class="submit-btn" type="submit" id="submitBtn">Submit</button><br>
            <button class="submit-btn" type="button" onclick="window.print()">Print</button>
        </form>
    </div>
</div>

<script>
    // JavaScript function to switch between patient details and record update section
    function showAddUpdateRecordSection() {
        document.getElementById("patientDetailsSection").style.display = "none";
        document.getElementById("addUpdateRecordSection").style.display = "block";
    }

    function goBack() {
        document.getElementById("patientDetailsSection").style.display = "block";
        document.getElementById("addUpdateRecordSection").style.display = "none";
    }
</script>
<div id="menu-icon">&#9776;</div>
<script>
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