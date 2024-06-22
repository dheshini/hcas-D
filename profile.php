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

// Fetch patient details based on email from session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT * FROM patients WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $patient = $result->fetch_assoc();
    } else {
        // Patient not found, redirect to login page
        header("location: patient_login.php");
        exit();
    }
} else {
    // If email is not set in session, redirect to login page
    header("location: patient_login.php");
    exit();
}

// Update patient information if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
// Retrieve form data
$phone = $_POST['phone'];
$gender = $_POST['gender'];
$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$homePhone = $_POST['home_phone'];
$officePhone = $_POST['office_phone'];
$dob = $_POST['dob'];
$race = $_POST['race'];
$nric_passport = $_POST['nric_passport'];
$emergencyNumber = $_POST['emergency_number']; // Add this line

// Update patient information in the database
$sql = "UPDATE patients SET phone = ?, gender = ?, first_name = ?, last_name = ?, home_phone = ?, office_phone = ?, date_of_birth = ?, race = ?, nric_passport = ?, emergency_number = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssss", $phone, $gender, $firstName, $lastName, $homePhone, $officePhone, $dob, $race, $nric_passport, $emergencyNumber, $email); // Add $emergencyNumber to the bind_param() function
$stmt->execute();

    // Redirect to profile page
    header("location: profile.php?success=true");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
.submit-btn {
  display: block;
  margin-left: auto;
  border: none;
  outline: none;
  background: #ff652f;
  font-size: 16px;
  text-transform: uppercase;
  letter-spacing: 1px;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

		.profile-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
        }

        .profile-info {
            width: 300px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
        }

        .profile-info p {
            margin: 0 0 10px;
			 text-align: left;
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

   <center><div class="profile-container">
        <div class="profile-info">
            <h2>Profile Information</h2>
            <p>Patient ID: <?php echo $patient['patient_id']; ?></p>
            <p>Email: <?php echo $patient['email']; ?></p>
            <p>Username: <?php echo $patient['username']; ?></p>
        </div></center>
		
		<div class="form">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>Edit Your Profile</h2>
				
				<label for="first_name">First Name<span style="color: red;">*</span></label>
                <div class="input-group">     
                    <input type="text" id="first_name" name="first_name" value="<?php echo $patient['first_name']; ?>">
                </div>

				<label for="last_name">Last Name<span style="color: red;">*</span></label>
                <div class="input-group">      
                    <input type="text" id="last_name" name="last_name" value="<?php echo $patient['last_name']; ?>">
                </div>
                
				<label for="nric_passport">IC/Passport<span style="color: red;">*</span></label>
				<div class="input-group">          
					<input type="text" id="nric_passport" name="nric_passport" value="<?php echo $patient['nric_passport']; ?>">
				</div>
				
				<label for="phone">Phone<span style="color: red;">*</span></label>
                <div class="input-group">      
                    <input type="text" id="phone" name="phone" value="<?php echo $patient['phone']; ?>">
                </div> 
				
				 <label for="home_phone">Home Phone<span style="color: red;">*</span></label>
                <div class="input-group">          
                    <input type="text" id="home_phone" name="home_phone" value="<?php echo $patient['home_phone']; ?>">
                </div>
				
               <label for="emergency_number">Emergency Number<span style="color: red;">*</span></label>
				<div class="input-group">
					<input type="text" id="emergency_number" name="emergency_number" value="<?php echo $patient['emergency_number']; ?>">
				</div>

				<label for="office_phone">Office Phone<span style="color: red;">*</span></label>	   
                <div class="input-group">    
                    <input type="text" id="office_phone" name="office_phone" value="<?php echo $patient['office_phone']; ?>">
                </div>
						
				 <label for="gender">Gender<span style="color: red;">*</span></label>
                <div class="input-group">       
                    <select id="gender" name="gender">
                        <option value="Male" <?php if ($patient['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($patient['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
          
				<label for="dob">Date of Birth<span style="color: red;">*</span></label>
                <div class="input-group">     
                    <input type="date" id="dob" name="dob" value="<?php echo $patient['date_of_birth']; ?>">
                </div>

				<label for="race">Race</label>
				<div class="input-group">          
					<select id="race" name="race">
						<option value="">Select Race</option>
						<option value="Malay" <?php if ($patient['race'] == 'Malay') echo 'selected'; ?>>Malay</option>
						<option value="Indian" <?php if ($patient['race'] == 'Indian') echo 'selected'; ?>>Indian</option>
						<option value="Chinese" <?php if ($patient['race'] == 'Chinese') echo 'selected'; ?>>Chinese</option>
						<option value="Other" <?php if ($patient['race'] == 'Other') echo 'selected'; ?>>Other</option>
					</select>
				</div>		

                <input class="submit-btn" type="submit" value="Update">
            </form>
        </div>
    </div>

	<script>
    // Check if URL parameter "success" is set to true
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    if (success === 'true') {
        // Display a success message
        alert('Profile updated successfully!');
    }
</script>

</body>
</html>
