<?php
session_start();
include '../config.php';
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

$patient_id = $_SESSION['patient_id'];
$success = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'edit') {
            // Edit existing record
            if (isset($_POST['record_id_to_edit'])) {
                $record_id_to_edit = $_POST['record_id_to_edit'];
                $sql_edit = "UPDATE patient_records SET medical_surgical_family_history = ?, surgery_year = ?, allergies = ?, allergies_specify = ?, past_medical_history = ?, clinical_summary = ? WHERE record_id = ? AND patient_id = ?";
                if ($stmt_edit = $conn->prepare($sql_edit)) {
                    $stmt_edit->bind_param("ssssssii", $_POST['medical_surgical_family_history'], $_POST['surgery_year'], $_POST['allergies'], $_POST['allergies_specify'], $_POST['past_medical_history'], $_POST['clinical_summary'], $record_id_to_edit, $patient_id);
                    if ($stmt_edit->execute()) {
                        $success = true;
                    }
                    $stmt_edit->close();
                }
            }
        } elseif ($_POST['action'] == 'add') {
            // Insert new record
            $sql_insert = "INSERT INTO patient_records (patient_id, medical_surgical_family_history, surgery_year, allergies, allergies_specify, past_medical_history, clinical_summary) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("issssss", $patient_id, $_POST['medical_surgical_family_history'], $_POST['surgery_year'], $_POST['allergies'], $_POST['allergies_specify'], $_POST['past_medical_history'], $_POST['clinical_summary']);
                if ($stmt_insert->execute()) {
                    $success = true;
                }
                $stmt_insert->close();
            }
        }
    }

    // Handle deletion of medical records
    if (isset($_POST['delete_record'])) {
        $record_id_to_delete = $_POST['record_id_to_delete'];
        $sql_delete = "DELETE FROM patient_records WHERE record_id = ? AND patient_id = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("ii", $record_id_to_delete, $patient_id);
            if ($stmt_delete->execute()) {
                $success = true; // Consider setting a success message here if needed
            }
            $stmt_delete->close();
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
    	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCAS - PATIENT MEDICAL REPORTS</title>
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
        HCAS - Patient Medical Reports
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
    <hr>
	</nav>
	
 <nav>
        <a href="profile.php"><i class="fas fa-user"></i>Account</a>
        <a href="settings.php"><i class="fas fa-cog"></i>Settings</a>
        <a href="address.php"><i class="fas fa-map-marker-alt"></i>Address</a>
        <a href="medical_reports.php"><i class="fas fa-file-medical-alt"></i>Medical Reports</a>
        <a href="inbox.php"><i class="fas fa-inbox"></i>Inbox</a>
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
  <?php
    // Display medical records
    if (!empty($medical_records)) {
        echo "<form class='medical-records-form' method='post' action=''>";
        foreach ($medical_records as $record) {
            echo "<div class='medical-record'>";
            // Display record details
            echo "<h4>Medical/Surgical History:</h4> <p>" . htmlspecialchars($record['medical_surgical_family_history']) . "</p>";
            echo "<h4>Surgery Year:</h4> <p>" . htmlspecialchars($record['surgery_year']) . "</p>";
            echo "<h4>Allergies:</h4> <p>" . htmlspecialchars($record['allergies']) . "</p>";
            echo "<h4>Allergies (specify):</h4> <p>" . htmlspecialchars($record['allergies_specify']) . "</p>";
            echo "<h4>Past Medical History:</h4> <p>" . htmlspecialchars($record['past_medical_history']) . "</p>";
            echo "<h4>Clinical Summary:</h4> <p>" . htmlspecialchars($record['clinical_summary']) . "</p>";

         // Add edit button
            echo "<div class='edit-button-container'>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='record_id_to_edit' value='" . $record['record_id'] . "'>";
            echo "<input type='hidden' name='medical_surgical_family_history' value='" . $record['medical_surgical_family_history'] . "'>";
            echo "<input type='hidden' name='surgery_year' value='" . $record['surgery_year'] . "'>";
            echo "<input type='hidden' name='allergies' value='" . $record['allergies'] . "'>";
            echo "<input type='hidden' name='allergies_specify' value='" . $record['allergies_specify'] . "'>";
            echo "<input type='hidden' name='past_medical_history' value='" . $record['past_medical_history'] . "'>";
            echo "<input type='hidden' name='clinical_summary' value='" . $record['clinical_summary'] . "'><br>";
           
            echo "</form>";
            echo "</div>";

            // Add delete button
            echo "<div class='delete-button-container'>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='record_id_to_delete' value='" . $record['record_id'] . "'>";
            echo "<input type='submit' name='delete_record' value='Delete' style='background: #ff652f; color: #fff;' onclick='return confirmDelete();'>&nbsp; ";
			echo "</form>";
            echo "</div>";

            echo "</div>"; // Close medical-record
        }
        echo "</form>";
    } else {
        echo "No records found.";
    }
    ?>
  <!-- Form for adding/updating medical records -->
    <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h4>Medical/Surgical History</h4>
        <textarea name="medical_surgical_family_history" rows="4" cols="50"></textarea>

        <h4>Surgery Year</h4>
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
        <div class="select-container">
            <select id="allergies" name="allergies" onchange="toggleAllergiesSpecify()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>
        <div id="allergies_specify_div" class="hidden">
            <label for="allergies_specify">Specify allergies:</label>
            <textarea id="allergies_specify" name="allergies_specify" rows="2" cols="50"></textarea>
        </div>

        <h4>Past Medical History</h4>
        <textarea id="past_medical_history" name="past_medical_history" rows="4" cols="50"></textarea>

        <h4>Clinical Summary</h4>
        <textarea name="clinical_summary" rows="4" cols="50"></textarea>

        <input type="hidden" name="action" value="add">
        <input type="submit" style="background: #ff652f; color: #fff;" value="Save & Update">
    </form>

   <!-- JavaScript function to confirm deletion -->
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }
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
