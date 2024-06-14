<?php
session_start();

include '../config.php';

// Function to update login attempts
function updateLoginAttempts($email, $attemptCount) {
    global $conn;
    $sql = "UPDATE doctors SET login_attempts = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $attemptCount, $email);
    $stmt->execute();
}

// Function to update last login time
function updateLastLoginTime($email) {
    global $conn;
    $sql = "UPDATE doctors SET last_login = NOW() WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
}

// Function to unlock account if lock duration expired
function unlockAccount($email) {
    global $conn;
    $sql = "UPDATE doctors SET account_locked = 0, lock_time = NULL WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
}


// Function to generate OTP
function generateOTP() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    $characters_length = strlen($characters);
    $otp_length = 6;
    $otp = '';
    $used_indexes = [];

    // Generate an OTP with unique characters
    for ($i = 0; $i < $otp_length; $i++) {
        $random_index = rand(0, $characters_length - 1);
        
        // Ensure the character at $random_index is not already used
        while (in_array($random_index, $used_indexes)) {
            $random_index = rand(0, $characters_length - 1);
        }
        
        // Add the character to the OTP
        $otp .= $characters[$random_index];
        $used_indexes[] = $random_index;
    }

    return $otp;
}

// Function to send OTP via email
function sendOTP($email, $otp) {
    $to = $email;
    $subject = 'One Time Password (OTP)';
	$message = '<html><body>';
	$message .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
	$message .= '<tr>';
	$message .= '<td style="padding: 20px; text-align: center; background-color: #f1f1f1;">';
	$message .= '<img src="http://localhost/logo.png" alt="Hannani Clinic Logo" style="width: 30px;">';
	$message .= '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="padding: 20px; text-align: center;">';
	$message .= "<h2>Verify Your OTP</h2>";
    $message .=  "<p>Your One Time Password (OTP) for login is: D -<strong> $otp </p>";
	$message .= "<p>Thank you for login with Hannani Clinic!</p>";
	$message .= '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="padding: 20px; text-align: center; background-color: #f1f1f1;">';
	$message .= '<p>If you have already entered your OTP, you can <a href="http://localhost/Hannani%20Project/user/doctor_login.php" style="color: #ff652f; text-decoration: underline;">log in here</a>.</p>';
	$message .= '<p>For any assistance, feel free to contact us at clinicdrhannani@gmail.com.</p>';
	$message .= '<p>Regards,<br>The Hannani Clinic Team</p>';
	$message .= '</td>';
	$message .= '</tr>';
	$message .= '</table>';
	$message .= '</body></html>';
	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: Hannani Clinic Team <clinicdrhannani@gmail.com>' . "\r\n";
    return mail($to, $subject, $message, $headers);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and password are set in the $_POST array
    if(isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

    // Check if it's an admin login
    if ($email === 'drhannaniadmin@gmail.com' && $password === 'drhannani_admin') {
        // Admin login, start session and redirect to admin home page
        $_SESSION['email'] = $email;
        header("location: ../admin/admin_home.php");
        exit();
    }

    // Check login attempts
    $sql = "SELECT * FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($result->num_rows == 1) {
        // Check if the account is locked
        if ($user['account_locked'] == 1) {
            // Check if lock duration has expired (20 minutes)
            $lockTime = strtotime($user['lock_time']);
            $currentTime = time();
            $lockDuration = 20 * 60; // 20 minutes in seconds
            if ($currentTime - $lockTime >= $lockDuration) {
                // Account lock duration expired, unlock the account
                unlockAccount($email);
            } else {
                // Account still locked, inform the user
                $remainingTime = $lockDuration - ($currentTime - $lockTime);
                $remainingMinutes = ceil($remainingTime / 60);
                echo "<script>alert('Your account is locked. Please try again after $remainingMinutes minutes.');</script>";
                exit();
            }
        }

        // Doctor exists, verify password
        if (password_verify($password, $user['password'])) {
            // Passwords match, update last login time
            updateLastLoginTime($email);
            // Reset login attempts
            updateLoginAttempts($email, 0);
			
			 // Generate OTP
            $otp = generateOTP();

            // Send OTP via email
            $otpSent = sendOTP($email, $otp);

            if ($otpSent) {
                // Store OTP in session for verification
                $_SESSION['otp'] = $otp;
				 $_SESSION['otp_time'] = time(); // Set OTP timestamp
                $_SESSION['email'] = $email;
                // Redirect to OTP verification page
                header("location: otp_verification.php");
                exit();
            } else {
                // Failed to send OTP, display error message
                echo "<script>alert('Failed to send OTP. Please try again.');</script>";
            }
        } else {

             // Invalid password, update login attempts
            $attemptCount = $user['login_attempts'] + 1;
            updateLoginAttempts($email, $attemptCount);
            if ($attemptCount >= 3) {
                // Lock the account
                $lockTime = date('Y-m-d H:i:s'); // Current time
                $sql = "UPDATE doctors SET account_locked = 1, lock_time = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $lockTime, $email);
                $stmt->execute();
                // Email notification to inform the user
                $to = $email;
                $subject = 'Account Locked';
                $message = '<html><body>';
                $message .= '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
                $message .= '<div style="background-color: #f4f4f4; padding: 20px; text-align: center;">';
                $message .= '<img src="http://localhost/Hannani%20Project/image/logo.png" alt="Hannani Clinic Logo" style="width: 150px;">';
                $message .= '</div>';
                $message .= '<div style="background-color: #fff; padding: 20px;">';
               	$message .= '<h2 style="text-align: center; color: #333;">Account Locked</h2>';
				$message .= '<p style="text-align: center; color: #333;">Your account has been locked due to multiple failed login attempts.</p>';
				$message .= '<p style="text-align: center; color: #333;">Please try again after 20 minutes. <a href="http://localhost/Hannani%20Project/user/patient_login.php" style="color: #ff652f; text-decoration: underline;">log in here</a></p>';
				$message .= '</div>';
                $message .= '<div style="background-color: #f4f4f4; padding: 20px; text-align: center;">';
                $message .= '<p style="color: #777;">For any assistance, feel free to contact us at clinicdrhannani@gmail.com.</p>';
                $message .= '<p style="color: #777;">Regards,<br>The Hannani Clinic Team</p>';
                $message .= '</div>';
                $message .= '</div>';
                $message .= '</body></html>';

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: Hannani Clinic Team <clinicdrhannani@gmail.com>' . "\r\n";

                mail($to, $subject, $message, $headers);
                // Your email notification code here
                echo "<script>alert('Your account is locked. Please try again after 20 minutes.');</script>";
                exit();
            } else {
                echo "<script>alert('Invalid email or password.');</script>";
            }
        }
    } else {
        // Doctor does not exist
        echo "<script>alert('Invalid email or password.');</script>";
    }
}}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>HCAS - DOCTOR SIGNIN</title>

	<style>
  body{
     background-image: url(../image/15.jpg);
  font-family: sans-serif;
    background-size: cover;}

  .login-wrapper {
  height: 90vh;
  width: 100vw;
  display: flex;
  justify-content: center;
  align-items: center;
}
.form {
  position: relative;
  width: 100%;
  max-width: 500px;
  padding: 50px 30px 80px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 10px;
  color: #fff;
  box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
  font-family: sans-serif;
  font-size:16px;
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
  font-size: 16px;
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
  /* Style for the select element */
        .custom-select {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 300px; /* Set maximum width */
            margin-bottom: 30px; /* Add some spacing */
        }

        /* Style for the select element itself */
        .custom-select select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #f1f1f1;
            color: #333;
            appearance: none; /* Remove default arrow */
            cursor: pointer; /* Add pointer cursor */
        }

        /* Style for the arrow symbol */
        .custom-select::after {
            content: '\25BC'; /* Unicode for a down triangle (▼) */
            font-size: 16px;
            color: #555;
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none; /* Prevent clicking on the arrow */
        }

        /* Style for the select element when focused */
        .custom-select select:focus {
            outline: none;
            border: 1px solid #ff652f;
        }
    .sub-text2{
    font-size: 17px;
    line-height: 27px;
    font-weight: 400;
    text-align: center;
    margin-top: 0;
    color:white;
}

.home-link {
    text-decoration: none;
    color: inherit;  /* Inherit the color from the parent */
}

.sub-text2{
    font-size: 18px;
    line-height: 27px;
    font-weight: 400;
    text-align: center;
    margin-top: 0;
	color:black;
}
 .footer-hashen {
            position: absolute;
            bottom: 0;
            left: 40%;
            font-size: 17px;
            animation: transitionIn-Y-over 0.5s;
        }
	</style>

</head>
<body>
    <div class="login-wrapper">
        <form class="form" action="doctor_login.php" method="post">
	<td>
		<br>
		<a href="../index.php" class="home-link">
			<i style="font-size:30px; color: white;" class="fa">&#xf015;</i>
		</a>
	</td>
            <h2>Doctor let's login</h2>
            <div class="input-group">
                <input type="email" name="email" required />
                <label>Email <span style="color: red;">*</span></label>
            </div>
             <div class="input-group">
        <input type="password" name="password" required />
        <label>Password <span style="color: red;">*</span></label> 
    </div>
    <button class="submit-btn" type="submit">Login</button>
			
        <p>Don't have an account? <a href="doctor_register.php" style="color:blue;">Register Now</a></p>
       <p><a href="forgot_password.php" style="color :#ff652f;" >Forgot Password?</a></p>
</form>
    </div>
          <br><br> <p class="sub-text2 footer-hashen">Rumah Sihat Kita Sdn Bhd (1361050-X)</p>

</body>

</html>