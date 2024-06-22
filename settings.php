<?php
session_start();
include '../config.php';

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

// Handle password change form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    // Validate current password, new password, and confirm password
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify if the new password is different from the current password
    if ($new_password === $current_password) {
        // New password is the same as the current password
        echo "<script>alert('New password must be different from the current password.');</script>";
    } else {
        // Fetch the current user's stored password from the database
        $email = $_SESSION['email'];
        $sql = "SELECT password FROM patients WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];
            
            // Verify if the current password matches the stored password
            if (password_verify($current_password, $stored_password)) {
                // Current password matches, proceed to update the password
                if ($new_password === $confirm_password) {
                    // Hash the new password
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update the password in the database
                    $sql = "UPDATE patients SET password = ? WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $hashed_new_password, $email);
                    if ($stmt->execute()) {
                        // Password updated successfully, redirect to success page
                        header("location: password_change_success.php");
                        exit();
                    } else {
                        // Error updating password
                        echo "<script>alert('Error updating password. Please try again.');</script>";
                    }
                } else {
                    // New password and confirm password do not match
                    echo "<script>alert('New password and confirm password do not match.');</script>";
                }
            } else {
                // Current password provided by the user is incorrect
                echo "<script>alert('Incorrect current password.');</script>";
            }
        } else {
            // User not found in the database
            header("location: patient_login.php");
            exit();
        }
    }
}

// Initialize variables for password error and new password
$password_err = '';
$new_password = '';

// Validate new password if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_settings'])) {
    // Validate new password
    if (empty(trim($_POST['new_password']))) {
        $password_err = '<span style="color: red;">Please enter a new password.</span>';
    } elseif (strlen(trim($_POST['new_password'])) < 6) {
        $password_err = '<span style="color: red;">New password must be at least 6 characters long.</span>';
    } else {
        $new_password = trim($_POST['new_password']);
        // Password strength validation
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+}{":;\'?\/><.,|`~])(?=.*[^\w\d\s]).{8,}$/', $new_password)) {
            $password_err = '<span style="color: red;">New password must contain at least 8 characters with at least one uppercase letter, one lowercase letter, one number, and one special character.</span>';
        }
    }
}

// Handle language/timezone settings form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_settings'])) {
    // Update language and timezone settings
    // Process language and timezone settings here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>HCAS - PATIENT CHANGE PASSWORD</title>
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
    </style>
</head>
<body>

    <header>
        HCAS - Patient Change Password
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
	
		 <!-- Password change form -->
   <div class="form">
    <h3>Change Password</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="input-group">
            <input type="password" id="current_password" name="current_password" required>
            <label for="current_password">Current Password:</label>
        </div>
        <?php if(isset($password_err)) { ?>
            <span style="color: red;"><?php echo $password_err; ?></span><br>
        <?php } ?>
		
        <div class="input-group">
            <input type="password" id="new_password" name="new_password" required onkeyup="checkPasswordStrength(this.value)">
            <label for="new_password">New Password:</label>
            <span id="password_strength"></span>
        </div>
		
         <div class="input-group">
		  <input type="password" id="confirm_password" name="confirm_password" required>
            <label for="confirm_password">Confirm Password:</label>
			 <span id="password_strength"></span>
         </div>
            <button class="submit-btn" type="submit" name="change_password">Submit</button>
        </form>
    </div>
            



<script>
    function checkPasswordStrength(password) {
        // Define the regular expressions for password strength
        let strongRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+}{":;\'?\/><.,|`~])(?=.*[^\w\d\s]).{8,}$/;
        let mediumRegex = /^(?=.*\d)(?=.*[a-zA-Z]).{6,}$/;

        // Check the password strength and display appropriate message
        if (strongRegex.test(password)) {
            document.getElementById("password_strength").innerHTML = '<span style="color: green;">Strong password</span>';
        } else if (mediumRegex.test(password)) {
            document.getElementById("password_strength").innerHTML = '<span style="color: orange;">Medium password</span>';
        } else {
            document.getElementById("password_strength").innerHTML = '<span style="color: red;">Weak password</span>';
        }
    }
</script>

</body>
</html>