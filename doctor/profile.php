<?php
session_start();
include '../config.php';

$email = $_SESSION['email'];
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCTOR - Doctors Profile</title>
    <style>
        /* General styles */
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
            border-radius: 4px;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
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
.profile-section select[name="gender"] {
    width: calc(100% - 20px);
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    color: #333;
}

.profile-section select[name="gender"] option {
    background-color: #fff;
    color: #333;
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

        .form select[name="appointment_id"] option {
            background-color: #fff;
            color: #333;
        }

        /* CSS for Doctor Information section */
        section {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }

        section h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #ff652f; /* Adjust color as needed */
        }

        section label {
            display: block;
            margin-bottom: 8px;
            color: #fff; /* Adjust color as needed */
        }

        section input {
            width: calc(100% - 20px); /* Adjust width as needed */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Additional styling for other input fields */
        .form input[type="text"],
        .form input[type="email"],
        .form input[type="number"],
        .form input[type="date"] {
            width: calc(100% - 20px); /* Adjust width as needed */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff; /* Adjust background color as needed */
            color: #333; /* Adjust text color as needed */
        }

 .form .submit-btn {
            width: 150px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #ff652f;
            cursor: pointer;
            color: #fff;
            font-weight: bold;
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
            width: 150px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #ff652f;
            cursor: pointer;
            color: #333;
            font-weight: bold;
        }

        .profile-section {
            margin-bottom: 20px;
        }

        .profile-section h3 {
            margin-bottom: 10px;
        }

        .profile-section input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <header>
        HCAS Appointment - Doctor Profile
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
        <a href="change_password.php">Change Password</a>  
		</div>
	
    <div id="sidebar">
        <a href="logout.php">SignOut</a>
        <h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>

  <div class="login-wrapper">
        <div class="form">
            <section>
                <h2>Doctor Information</h2>
                <label for="doctor_id">Doctor ID:</label>
                <input type="text" id="doctor_id" name="doctor_id" value="<?php echo $row['doctor_id']; ?>" readonly><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" readonly><br><br>
            </section>

            <form action="update_profile.php" method="post">
                <section class="profile-section">
                    <h3>Profile Information</h3>
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $row['phone']; ?>"><br><br>

                   <label for="gender">Gender:</label>
					<select id="gender" name="gender">
						<option value="male" <?php if ($row['gender'] === 'male') echo 'selected'; ?>>Male</option>
						<option value="female" <?php if ($row['gender'] === 'female') echo 'selected'; ?>>Female</option>
					</select><br><br>

                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" value="<?php echo $row['age']; ?>"><br><br>

                    <label for="home_address">Home Address:</label>
                    <input type="text" id="home_address" name="home_address" value="<?php echo $row['home_address']; ?>"><br><br>

                    <label for="identity_card">Identity Card:</label>
                    <input type="text" id="identity_card" name="identity_card" value="<?php echo $row['identity_card']; ?>"><br><br>

                    <label for="dateOfBirth">Date of Birth:</label>
                    <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo $row['dateOfBirth']; ?>"><br><br>

                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo $row['first_name']; ?>"><br><br>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo $row['last_name']; ?>"><br><br>

                    <label for="max_appointments_per_day">Max Appointments per Day:</label>
                    <input type="number" id="max_appointments_per_day" name="max_appointments_per_day" value="<?php echo $row['max_appointments_per_day']; ?>"><br><br>
                </section>

                <input class="submit-btn" type="submit" value="Update Profile">
            </form>
        </div>
    </div>
	
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
</body>
</html>
