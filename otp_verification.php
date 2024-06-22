<?php
session_start();
include('../session.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: patient_login.php");
    exit();
}

// Check if OTP verification period is exceeded (1 minute)
if (isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time'] > 60)) {
    // OTP verification period exceeded, redirect user to login page
    unset($_SESSION['otp']); // Clear OTP from session
    unset($_SESSION['otp_time']); // Clear OTP timestamp from session
    echo '<script>alert("The OTP verification period has expired. Please request a new OTP."); window.location.href = "patient_login.php";</script>';
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if OTP entered by the user matches the OTP stored in session
    if ($_POST['otp'] === $_SESSION['otp']) {
        // OTP verification successful, check if it's a temporary password or regular password
        if (isset($_SESSION['temporary_password']) && $_SESSION['temporary_password'] === 1) {
            // Temporary password, redirect user to settings.php to change password
            unset($_SESSION['otp']); // Clear OTP from session
            unset($_SESSION['otp_time']); // Clear OTP timestamp from session
            header("location: settings.php");
            exit();
        } else {
            // Regular password, redirect user to user_home.php
            unset($_SESSION['otp']); // Clear OTP from session
            unset($_SESSION['otp_time']); // Clear OTP timestamp from session
            header("location: user_home.php");
            exit();
        }
    } else {
        // OTP verification failed, display error message
        $error = "Invalid OTP. Please try again.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCAS - OTP VERIFICATION </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
    </style>
</head>

<body>
    <div class="login-wrapper">
        <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>OTP Verification</h2>
            <div class="input-group">
                <input type="text" id="otp" name="otp" required />
                <label>Enter OTP : <span style="color: red;">*</span></label><br>
            </div>
            <input class="submit-btn" type="submit" value="Verify OTP">
            <?php if (isset($error)) { echo "<p style='color: red; text-align: center;'>$error</p>"; } ?>
        </form>
    </div>
</body>

</html>