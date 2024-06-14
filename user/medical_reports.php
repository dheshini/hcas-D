<?php
session_start();
include '../config.php';
include('../session.php');
// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$success = false; // Initialize $success variable

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all necessary POST variables are set
    if (isset($_POST['medical_surgical_family_history'], $_POST['surgery_year'], $_POST['allergies'], $_POST['allergies_specify'], $_POST['past_medical_history'], $_POST['clinical_summary'])) {
        // Assign POST data to variables
        $medical_surgical_family_history = $_POST['medical_surgical_family_history'];
        $surgery_year = $_POST['surgery_year'];
        $allergies = $_POST['allergies'];
        $allergies_specify = $_POST['allergies_specify'];
        $past_medical_history = $_POST['past_medical_history'];
        $clinical_summary = $_POST['clinical_summary'];

        // Check if patient already has records
        $sql_check = "SELECT * FROM patient_records WHERE patient_id = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("i", $patient_id);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                // Update existing record
                $sql_update = "UPDATE patient_records SET medical_surgical_family_history = ?, surgery_year = ?, allergies = ?, allergies_specify = ?, past_medical_history = ?, clinical_summary = ? WHERE patient_id = ?";
                if ($stmt_update = $conn->prepare($sql_update)) {
                    $stmt_update->bind_param("ssssssi", $medical_surgical_family_history, $surgery_year, $allergies, $allergies_specify, $past_medical_history, $clinical_summary, $patient_id);
                    if ($stmt_update->execute()) {
                        $success = true;
                    }
                    $stmt_update->close();
                }
            } else {
                // Insert new record
                $sql_insert = "INSERT INTO patient_records (patient_id, medical_surgical_family_history, surgery_year, allergies, allergies_specify, past_medical_history, clinical_summary) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                if ($stmt_insert = $conn->prepare($sql_insert)) {
                    $stmt_insert->bind_param("issssss", $patient_id, $medical_surgical_family_history, $surgery_year, $allergies, $allergies_specify, $past_medical_history, $clinical_summary);
                    if ($stmt_insert->execute()) {
                        $success = true;
                    }
                    $stmt_insert->close();
                }
            }
            $stmt_check->close();
        }
    }

    // Handle deletion of medical records
    if (isset($_POST['delete_record'])) {
        $record_id_to_delete = $_POST['record_id_to_delete'];

        // Perform the deletion query
        $sql_delete = "DELETE FROM patient_records WHERE record_id = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $record_id_to_delete);
            if ($stmt_delete->execute()) {
                $delete_success = true;
            } else {
                $delete_error = "Error deleting record: " . $conn->error;
            }
            $stmt_delete->close();
        } else {
            $delete_error = "Error preparing deletion statement: " . $conn->error;
        }
    }
}

// Fetch patient's medical records
$sql_fetch = "SELECT * FROM patient_records WHERE patient_id = ?";
$medical_records = array();
if ($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $patient_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    while ($row = $result->fetch_assoc()) {
        $medical_records[] = $row;
    }
    $stmt_fetch->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCAS - PATIENT PROFILE</title>
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
.profile-buttons {
    text-align: center;
    margin: 20px 0;
}

.profile-buttons a {
    display: inline-block;
    margin: 5px;
    padding: 10px 20px;
    background-color: rgba(0, 0, 0, 0.5);
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.profile-buttons a:hover {
    background-color: rgba(255, 255, 255, 0.1);
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

form h4 {
    margin-top: 0;
}

form textarea,
form input[type="text"],
form select {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
    border: 1px solid #ccc;
}

form input[type="submit"] {
    background-color: #3498db;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #2980b9;
}

.hidden {
    display: none;
}


.medical-record {
    width: 50%;
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 7px;
    padding: 20px;
    margin: 20px auto; /* Center horizontally */
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.medical-record h4 {
    font-size: 18px;
    color: #333;
    margin: 0;
    flex: 1 1 200px;
}

.medical-record p {
    font-size: 16px;
    color: #555;
    margin: 0;
    flex: 2 1 300px;
}

select {
    appearance: none;
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    font-size: 16px;
    border-radius: 4px;
    width: 100%;
    background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16"%3E%3Cpath fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/%3E%3C/svg%3E');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
}

select:focus {
    border-color: #3498db;
    outline: none;
}

.select-container {
    position: relative;
    width: 100%;
}

.select-container::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 15px;
    width: 0;
    height: 0;
    pointer-events: none;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #000;
    transform: translateY(-50%);
}

    </style>
</head>
<body>
    <header>
        HCAS - Patient Profile
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

    <div class="profile-buttons">
        <a href="settings.php">Settings</a>
        <a href="address.php">Address</a>
        <a href="medical_reports.php">Medical Reports</a>
        <a href="inbox.php">Inbox</a>
    </div>

   <div id="sidebar">
        <a href="logout.php">SignOut<a>
		<h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>
	
<?php
// Display medical records
if (!empty($medical_records)) {
    echo "<form class='medical-records-form' method='post' action=''>";
    foreach ($medical_records as $record) {
        echo "<div class='medical-record'>";
        // Check if record fields are not null before accessing
        echo "<h4>Medical/Surgical History:</h4> <p>" . (!empty($record['medical_surgical_family_history']) ? htmlspecialchars($record['medical_surgical_family_history']) : "") . "</p>";
        echo "<h4>Surgery Year:</h4> <p>" . (!empty($record['surgery_year']) ? htmlspecialchars($record['surgery_year']) : "") . "</p>";
        echo "<h4>Allergies:</h4> <p>" . (!empty($record['allergies']) ? htmlspecialchars($record['allergies']) : "") . "</p>";
		echo "<h4>Allergies (specify):</h4> <p>" . (!empty($record['allergies_specify']) ? htmlspecialchars($record['allergies_specify']) : "") . "</p>";
        echo "<h4>Past Medical History:</h4> <p>" . (!empty($record['past_medical_history']) ? htmlspecialchars($record['past_medical_history']) : "") . "</p>";
        echo "<h4>Clinical Summary:</h4> <p>" . (!empty($record['clinical_summary']) ? htmlspecialchars($record['clinical_summary']) : "") . "</p>";

        // Add delete button
		echo "<div class='delete-button-container'>";
        echo "<form action='' method='post'>";
        echo "<input type='hidden' name='record_id_to_delete' value='" . $record['record_id'] . "'>";
		echo "<br><input type='submit' name='delete_record' value='Delete'style='background: #ff652f; color: #fff;' onclick='return confirmDelete();'>";
        echo "</form>";
        echo "</div>";

        echo "</div>"; // Close medical-record
    }
    echo "</form>";
} else {
    echo "No records found.";
}
?>
<script>
        // JavaScript function to confirm deletion
      function confirmDelete() {
    return confirm("Are you sure you want to delete this record?");
}
    </script>
  <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h4>Medical/Surgical History</h4>
        <textarea name="medical_surgical_family_history" rows="4" cols="50"></textarea>


        <h4>Surgery</h4>
				<label for="surgery_year">Year:</label>
		<div class="select-container">
			<select id="surgery_year" name="surgery_year">
					<?php
					$currentYear = date("Y");
					for ($year = 1920; $year <= $currentYear; $year++) {
						echo "<option value=\"$year\">$year</option>";
					}
					?>
			 </select>
		</div>

			   <h4>Allergies</h4>
		<label for="allergies">Do you have any allergies?</label>
		<div class="select-container">
			<select id="allergies" name="allergies" onchange="toggleAllergiesSpecify()">
    <option value="No">No</option>
    <option value="Yes">Yes</option>
</select>

		</div>
		<br>
		
       <div id="allergies_specify_div" class="hidden">
    <label for="allergies_specify">If yes, specify:</label>
    <textarea id="allergies_specify" name="allergies_specify" rows="2" cols="50"></textarea>
</div>
        <h4>Past Medical History</h4>
        <label for="past_medical_history">Do you have a history of any of the following problems? (e.g., smoking, headaches, depression, stroke, diabetes, etc.)</label>
        <textarea id="past_medical_history" name="past_medical_history" rows="4" cols="50"></textarea>

        <h4>Clinical Summary</h4>
        <textarea name="clinical_summary" rows="4" cols="50"></textarea>

        <input type="submit" style='background: #ff652f; color: #fff;' value="Save & Update">
    </form>


    <?php if ($success): ?>
        <script>
            alert("Your information has been saved successfully.");
        </script>
    <?php endif; ?>

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
			
<script>
    function toggleAllergiesSpecify() {
        console.log("Function called");
        const allergies = document.getElementById('allergies');
        console.log("Allergies value:", allergies.value);
        const allergiesSpecifyDiv = document.getElementById('allergies_specify_div');
        if (allergies.value === 'Yes') {
            allergiesSpecifyDiv.style.display = 'block'; // Show the allergies specify textarea
        } else {
            allergiesSpecifyDiv.style.display = 'none'; // Hide the allergies specify textarea
        }
    }

    // Call toggleAllergiesSpecify() when the page loads
    window.onload = toggleAllergiesSpecify;

    // Call toggleAllergiesSpecify() when the value of allergies changes
    document.getElementById('allergies').addEventListener('change', toggleAllergiesSpecify);
</script>

<script>
// Call toggleAllergiesSpecify() when the page loads
window.onload = toggleAllergiesSpecify;

// Call toggleAllergiesSpecify() when the value of allergies changes
document.getElementById('allergies').addEventListener('change', toggleAllergiesSpecify);
</script>

</body>
</html>
