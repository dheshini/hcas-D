<?php
session_start();

include '../config.php';

// Initialize variables
$username = $email = $phone = $password = $cpassword = '';
$username_err = $email_err = $phone_err = $password_err = $cpassword_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter a username.';
    } else {
        $username = trim($_POST['username']);
        // Check if username contains at least one letter, one symbol, and one number
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d\s])\S{6,}$/', $username)) {
            $username_err = 'Username should contain at least one letter, one symbol, one number, and be at least 6 characters long.';
        }
        // Check if username is the same as the password
        if ($username === $password) {
            $username_err = 'Username should not be the same as the password.';
        }
    }

    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } else {
        $email = trim($_POST['email']);
        // Check if email already exists
        $sql_email_check = "SELECT email FROM doctors WHERE email = ?";
        $stmt_email_check = mysqli_prepare($conn, $sql_email_check);
        mysqli_stmt_bind_param($stmt_email_check, "s", $email);
        mysqli_stmt_execute($stmt_email_check);
        mysqli_stmt_store_result($stmt_email_check);
        if (mysqli_stmt_num_rows($stmt_email_check) > 0) {
            $email_err = 'This email is already registered.';
        }
        mysqli_stmt_close($stmt_email_check);
    }

    // Validate phone number
    if (empty(trim($_POST['phone']))) {
        $phone_err = 'Please enter your phone number.';
    } else {
        $phone = trim($_POST['phone']);
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = 'Password must be at least 6 characters long.';
    } else {
        $password = trim($_POST['password']);
        // Password strength validation
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+}{":;\'?\/><.,|`~])(?=.*[^\w\d\s]).{8,}$/', $password)) {
            $password_err = 'Password must contain at least 8 characters with at least one uppercase letter, one lowercase letter, one number, and one special character.';
        }
    }

    // Validate confirm password
    if (empty(trim($_POST['cpassword']))) {
        $cpassword_err = 'Please confirm your password.';
    } else {
        $cpassword = trim($_POST['cpassword']);
        if ($password != $cpassword) {
            $cpassword_err = 'Passwords do not match.';
        }
    }

    // If there are no errors, proceed with registration
    if (empty($username_err) && empty($email_err) && empty($phone_err) && empty($password_err) && empty($cpassword_err)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate verification code
        $verification_code = uniqid();

       // Set expiration time to 2 minutes from now
        $expiration_time = strtotime('+1 minutes');
		$expiration_date = date('Y-m-d H:i:s', $expiration_time);

        // Prepare SQL statement
        $sql = "INSERT INTO doctors (username, email, phone, password, verification_code, verification_code_expires) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_email, $param_phone, $param_password, $param_verification_code, $param_expiration_time);
            $param_username = $username;
            $param_email = $email;
            $param_phone = $phone;
            $param_password = $hashed_password;
            $param_verification_code = $verification_code;
            $param_expiration_time = date('Y-m-d H:i:s', $expiration_time);


            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Prepare verification email
                $subject = "Welcome to Hannani Clinic!";
                $message = '<html><body>';
                $message .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
                $message .= '<tr>';
                $message .= '<td style="padding: 20px; text-align: center; background-color: #f1f1f1;">';
                $message .= '<img src="http://localhost/logo.png" alt="Hannani Clinic Logo" style="width: 30px;">';
                $message .= '</td>';
                $message .= '</tr>';
                $message .= '<tr>';
                $message .= '<td style="padding: 20px; text-align: center;">';
				$message .= "<h2>Hello $username,</h2>";
				$message .= "<p>Thank you for registering with Hannani Clinic!</p>";
				$message .= '<p>Your verification code will expire in 12 Hours. Please click the button below to verify your email address:</p>';$message .= '<a href="http://localhost/Hannani%20Project/doctor/verify.php?code='.$verification_code.'" style="background-color: #ff652f; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Verify Email</a>';$message .= '</td>';
                $message .= '</tr>';
                $message .= '<tr>';
                $message .= '<td style="padding: 20px; text-align: center; background-color: #f1f1f1;">';
                $message .= '<p>If you have already verified your email, you can <a href="http://localhost/Hannani%20Project/doctor/doctor_login.php" style="color: #ff652f; text-decoration: underline;">log in here</a>.</p>';
                $message .= '<p>For any assistance, feel free to contact us at clinicdrhannani@gmail.com.</p>';
                $message .= '<p>Regards,<br>The Hannani Clinic Team</p>';
                $message .= '</td>';
                $message .= '</tr>';
                $message .= '</table>';
                $message .= '</body></html>';

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: Hannani Clinic Team <clinicdrhannani@gmail.com>' . "\r\n";

if (mail($email, $subject, $message, $headers)) {
      echo "<script>alert('Successful registration. Check your email to verify.');</script>";
            } else {
                echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
            }
        }
    }
    // Close connection
    mysqli_close($conn);
}

// Check if verification code has expired
if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    $sql_check_code = "SELECT verification_code_expires FROM doctors WHERE verification_code = ?";
    $stmt_check_code = mysqli_prepare($conn, $sql_check_code);
    mysqli_stmt_bind_param($stmt_check_code, "s", $verification_code);
    mysqli_stmt_execute($stmt_check_code);
    mysqli_stmt_bind_result($stmt_check_code, $verification_code_expires);
    mysqli_stmt_fetch($stmt_check_code);

    if ($verification_code_expires) {
        if (strtotime($verification_code_expires) < time()) {
            echo "<script>alert('Could not verify your email. Verification code has expired. Please register again.');</script>";
        }
    }

    mysqli_stmt_close($stmt_check_code);
}}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <title>HCAS - DOCTOR SIGNUP</title>

    <style>
        body {
            background-image: url(../image/15.jpg);
            font-family: sans-serif;
            background-size: cover;
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
            padding: 50px 30px 80px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
            font-family: sans-serif;
            font-size: 16px;
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

        .form .input-group input:focus+label,
        .form .input-group input:valid+label {
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
            max-width: 300px;
            /* Set maximum width */
            margin-bottom: 30px;
            /* Add some spacing */
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
            appearance: none;
            /* Remove default arrow */
            cursor: pointer;
            /* Add pointer cursor */
        }

        /* Style for the arrow symbol */
        .custom-select::after {
            content: '\25BC';
            /* Unicode for a down triangle (▼) */
            font-size: 16px;
            color: #555;
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
            /* Prevent clicking on the arrow */
        }

        /* Style for the select element when focused */
        .custom-select select:focus {
            outline: none;
            border: 1px solid #ff652f;
        }

        .sub-text2 {
            font-size: 17px;
            line-height: 27px;
            font-weight: 400;
            text-align: center;
            margin-top: 0;
            color: white;
        }

        .home-link {
            text-decoration: none;
            color: inherit;
            /* Inherit the color from the parent */
        }

        .sub-text2 {
            font-size: 18px;
            line-height: 27px;
            font-weight: 400;
            text-align: center;
            margin-top: 0;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <td>
                <br>
                <a href="../index.php" class="home-link">
                    <i style="font-size:30px; color: white;" class="fa">&#xf015;</i>
                </a>
            </td>
            <h2>Let's Register Doctor in HCAS</h2>
            <div class="input-group">
                <input type="text" name="username" value="<?php echo $username; ?>" required />
                <label for="name">User Name <span style="color: red;">*</span></label> <!-- Add asterisk symbol -->
                <span class="error" style="color: red"><?php echo $username_err; ?></span>
            </div>


            <div class="input-group">
                <input type="email" name="email" value="<?php echo $email; ?>" required />
                <label for="email">Email <span style="color: red;">*</span></label> <!-- Add asterisk symbol -->
                <!-- Your error message handling for email -->
                <?php if (!empty($email_err)) echo "<span style='color: red' class='error'>$email_err</span>"; ?>
            </div>
            <br>

            <div class="input-group">
                <input type="text" name="phone" value="<?php echo $phone; ?>" />
                <label for="phone">Phone Number <span style="color: red;">*</span></label> <!-- Add asterisk symbol -->
                <!-- Your error message handling for phone -->
                <?php if (!empty($phone_err)) echo "<span class='error'>$phone_err</span>"; ?>
            </div>

            <div class="input-group">
                <input type="password" name="password" value="<?php echo $password; ?>" required onkeyup="checkPasswordStrength(this.value)" />
                <label for="phone">Password <span style="color: red;">*</span></label> <!-- Add asterisk symbol -->
                <!-- Your error message handling for password -->
                <?php if (!empty($password_err)) echo "<span class='error'>$password_err</span>"; ?>
            </div>
            <div id="password-strength-indicator"></div> <!-- Password strength indicator -->
            <br>
            <div class="input-group">
                <input type="password" name="cpassword" value="<?php echo $cpassword; ?>" required />
                <label for="phone">Confirm Password <span style="color: red;">*</span></label> <!-- Add asterisk symbol -->
                <!-- Your error message handling for confirm password -->
                <?php if (!empty($cpassword_err)) echo "<span class='error'>$cpassword_err</span>"; ?>
            </div>
            <br><br>
            <button class="submit-btn" type="submit" name="register">Register</button>
            Already have an account? <a href="doctor_login.php" style="color: blue">Login Now</a>
        </form>
    </div>

    <script>
        function checkPasswordStrength(password) {
            var strengthIndicator = document.getElementById('password-strength-indicator');
            var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+}{":;\'?\/><.,|`~])(?=.*[^\w\d\s]).{8,}$/;

            if (password.match(regex)) {
                strengthIndicator.innerHTML = '✅ Password strength: Strong';
                strengthIndicator.style.color = 'green';
            } else {
                strengthIndicator.innerHTML = '❌ Password strength: Weak';
                strengthIndicator.style.color = 'red';
            }
        }
    </script>


    <br><br>
    <p class="sub-text2 footer-hashen">Rumah Sihat Kita Sdn Bhd (1361050-X)</p>

</body>

</html>
