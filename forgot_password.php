<?php
session_start();

include '../config.php';
include('../session.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM patients WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Generate a temporary password
        $tempPassword = generateTemporaryPassword();

        // Hash the temporary password
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

        // Update the user's password and set temporary password flag in the database
        $sql = "UPDATE patients SET password = ?, temporary_password = TRUE, temporary_password_timestamp = NOW() WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();

        // Send email with temporary password
        sendTemporaryPasswordEmail($email, $tempPassword);

        // Display popup message and redirect to login page
        echo "<script>alert('An email with a temporary password has been sent to your email address. Please check your inbox and change your password in settings');
        window.location.href = 'patient_login.php';</script>";
    } else {
        // Email does not exist in the database
        echo "<script>alert('Email does not exist.');</script>";
    }
}

function generateTemporaryPassword() {
    // Define the pool of characters for the password
    $uppercaseChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
    $numberChars = '0123456789';
    $symbolChars = '!@#$%^&*()-_=+';

    // Combine all character pools
    $chars = $uppercaseChars . $lowercaseChars . $numberChars . $symbolChars;

    // Initialize the temporary password variable
    $tempPassword = '';

    // Generate the password with the specified criteria
    $tempPassword .= $uppercaseChars[rand(0, strlen($uppercaseChars) - 1)];
    $tempPassword .= $lowercaseChars[rand(0, strlen($lowercaseChars) - 1)];
    $tempPassword .= $numberChars[rand(0, strlen($numberChars) - 1)];
    $tempPassword .= $symbolChars[rand(0, strlen($symbolChars) - 1)];

    // Fill the rest of the password with random characters from the combined pool
    for ($i = 0; $i < 4; $i++) {
        $tempPassword .= $chars[rand(0, strlen($chars) - 1)];
    }

    // Shuffle the characters to ensure randomness
    $tempPassword = str_shuffle($tempPassword);

    return $tempPassword;
}

function sendTemporaryPasswordEmail($email, $tempPassword) {
    $to = $email;
    $subject = 'Temporary Password';

    // Calculate the expiration time (1 minute from now)
    $expirationTime = date('Y-m-d H:i:s', strtotime('+1 minute'));

	$message = '<html><body>';
	$message .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
	$message .= '<tr>';
	$message .= '<td style="padding: 20px; text-align: center; background-color: #f1f1f1;">';
	$message .= '<h2>Welcome to Clinic Dr Hannani Parit Raja !</h2>';
	$message .= '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
    $message .= '<div style="background-color: #fff; padding: 20px;">';
    $message .= '<h2 style="text-align: center; color: #333;">Temporary Password</h2>';
    $message .= '<p style="text-align: center; color: #333;">Hello,</p>';
    $message .= '<p style="text-align: center; color: #333;">Your temporary password is: D - <strong>' . $tempPassword . '</strong></p>';
    $message .= '<p style="text-align: center; color: #333;">Please use this temporary password to login & reset your password <a href="http://localhost/Hannani%20Project/user/patient_login.php" style="color: #ff652f; text-decoration: underline;">log in here</a></p>';
    $message .= '<p style="text-align: center; color: #333;">This temporary password will expire at: ' . $expirationTime . '</p>'; // Include expiration time
    $message .= '</div>';
    $message .= '<div style="background-color: #f4f4f4; padding: 20px; text-align: center;">';
    $message .= '<p style="color: #777;">For any assistance, feel free to contact us at clinicdrhannani@gmail.com.</p>';
    $message .= '<p style="color: #777;">Regards,<br>The Hannani Clinic Team</p>';
    $message .= '</div>';
    $message .= '</div>';
    $message .= '</body>';
    $message .= '</html>';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Hannani Clinic Team <clinicdrhannani@gmail.com>' . "\r\n";

    mail($to, $subject, $message, $headers);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	 <script src="https://apis.google.com/js/platform.js" async defer></script>
	<title>HCAS - PATIENT FORGOT PASSWORD</title>
<style>
  body{
     background-image: url(../image/14.jpg);
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
}
 .footer-hashen {
            position: absolute;
            bottom: 0;
            left: 45%;
            font-size: 17px;
            animation: transitionIn-Y-over 0.5s;
        }
		.form h6 {
  text-align: center;
  letter-spacing: 1px;
  margin-bottom: 2rem;
  color: white;
}
	</style>

</head>

<body>
    <div class="login-wrapper">
        <form class="form"	action="forgot_password.php" method="post">
	<td>
		<br>
		<a href="../index.php" class="home-link">
			<i style="font-size:30px; color: white;" class="fa">&#xf015;</i>
		</a>
	</td>

    <h2>Reset your password</h2>
	<h6>If you have forgotten your password, don't worry! You can request a temporary password by entering your email address below. We will send a temporary password to the email address associated with your account.</h6>	<div class="input-group">
		<input type="email" name="email" required>
        <label>Email:</label>
	</div>
        <button class="submit-btn" type="submit">Reset Password</button>
    </form>
	</div>
</body>
</html>
