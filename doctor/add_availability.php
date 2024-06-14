<?php
session_start();

// Include config file
include '../config.php';

// Check if the doctor is logged in and retrieve doctor ID from session
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $date = $_POST["date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    
    // Convert times to 24-hour format for storage in the database
    $start_time_24 = date("H:i:s", strtotime($start_time));
    $end_time_24 = date("H:i:s", strtotime($end_time));
    
    // Validate that end time is greater than start time
    if ($end_time_24 <= $start_time_24) {
        $error_message = "Error: End time must be greater than start time.";
    } else {
        $selected_services = $_POST["services"]; // Retrieve selected services

        // Insert availability data into the database with doctor_id
        $sql = "INSERT INTO doctor_availability (doctor_id, date, start_time, end_time, service_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Check if the prepared statement was successfully created
        if ($stmt) {
            // Bind parameters and execute the statement
            $stmt->bind_param("isssi", $doctor_id, $date, $start_time_24, $end_time_24, $service_id);
            
            // Loop through selected services and insert availability for each service
            foreach ($selected_services as $service_id) {
                $stmt->execute();
            }

            // Check if the execution was successful
            if ($stmt->affected_rows > 0) {
                // Redirect to availability success page upon successful insertion
                header("Location: view_availability.php");
                exit();
            } else {
                // Display an error message if insertion failed
                $error_message = "Error: Unable to insert availability data.";
            }
        } else {
            // Display an error message if statement preparation failed
            $error_message = "Error: Unable to prepare statement.";
        }

        // Close statement
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCTOR - Add Availability</title>
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

        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form {
            position: relative;
            width: 90%;
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
            width: 100%;
            margin-bottom: 20px;
        }

        .form .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form .input-group input,
        .form .input-group select {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: transparent;
            color: inherit;
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

        .input-group .custom-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .input-group .custom-checkbox input {
            display: none;
        }

        .input-group .custom-checkbox label {
            position: relative;
            padding-left: 25px;
            cursor: pointer;
            font-size: 14px;
        }

        .input-group .custom-checkbox label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            border: 2px solid #fff;
            border-radius: 4px;
            background-color: transparent;
            transition: background-color 0.3s ease;
        }

        .input-group .custom-checkbox input:checked + label::before {
            background-color: #ff652f;
            border-color: #ff652f;
        }

        .input-group .custom-checkbox input:checked + label::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
    </style>
</head>

<body>
    <header>
        HCAS Appointment - Add Availability
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

    <div class="profile-buttons">
        <a href="view_availability.php">View Availability</a>
    </div>

    <div id="sidebar">
        <a href="logout.php">SignOut<a>
        <h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>

    <div class="login-wrapper">
        <form class="form" id="availability-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Add Doctor Availability</h2>
            <?php if (isset($error_message)) : ?>
                <p><?php echo $error_message; ?></p>
            <?php endif; ?>

            <div class="input-group">
                <label for="services">Specialist :</label>
                <?php
                // Fetch services from the database
                $sql_services = "SELECT * FROM services";
                $result_services = $conn->query($sql_services);
                if ($result_services->num_rows > 0) {
                    while ($row_services = $result_services->fetch_assoc()) {
                        echo "<div class='custom-checkbox'>";
                        echo "<input type='checkbox' name='services[]' value='" . $row_services['service_id'] . "' id='service_" . $row_services['service_id'] . "'>";
                        echo "<label for='service_" . $row_services['service_id'] . "'>" . $row_services['name'] . "</label>";
                        echo "</div>";
                    }
                }
                ?>
            </div>

            <div class="input-group">
                <label for="date">Date :</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="input-group">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>
            </div>

            <div class="input-group">
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>
            </div>

            <button class="submit-btn" type="submit">Add Availability</button>
        </form>
    </div>

    <script>
        // Function to check if end time is greater than start time
        function validateTime() {
            var startTime = document.getElementById("start_time").value;
            var endTime = document.getElementById("end_time").value;

            if (startTime >= endTime) {
                alert("End time must be greater than start time.");
                return false;
            }
            return true;
        }

        // Add event listener to the form for form submission
        document.getElementById("availability-form").addEventListener("submit", function (event) {
            // Prevent form submission if validation fails
            if (!validateTime()) {
                event.preventDefault();
            }
        });

        // Function to check if the selected date is in the past
        document.getElementById("date").addEventListener("change", function () {
            var selectedDate = new Date(this.value);
            var currentDate = new Date();

            if (selectedDate < currentDate) {
                alert("Please select a future date.");
                this.value = "";
            }
        });

        // Function to check if the selected time is within the allowed range
        document.getElementById("start_time").addEventListener("change", function () {
            if (this.value < "08:00" || this.value > "22:00") {
                alert("Availability can only be set between 8:00 AM and 10:00 PM.");
                this.value = "";
            }
        });

        document.getElementById("end_time").addEventListener("change", function () {
            if (this.value < "08:00" || this.value > "22:00") {
                alert("Availability can only be set between 8:00 AM and 10:00 PM.");
                this.value = "";
            }
        });
    </script>

    <script>
        // Function to display popup alert message after form submission
        function showAlert() {
            alert("Availability added successfully!");
        }

        // Add event listener to the form for form submission
        document.getElementById("availability-form").addEventListener("submit", showAlert);
    </script>

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
