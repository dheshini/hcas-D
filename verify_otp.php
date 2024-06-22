<?php
session_start();
include '../config.php';
include('../session.php');

$email = $_SESSION['email'];

// Function to send OTP via email using PHPMailer
function sendOTP($email, $otp) {
    $mail = new PHPMailer;

    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;
    $mail->Username = 'clinicdrhannani@gmail.com';  // SMTP username
    $mail->Password = '';    // SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('clinicdrhannani@gmail.com', 'HCAS');
    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = 'Your OTP for Password Change';
    $mail->Body    = 'Your OTP for password change is: ' . $otp . '. It will expire in 5 minutes.';

    if(!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

// Handle OTP generation and sending
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_otp'])) {
    $otp = rand(100000, 999999);
    $otp_expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Store OTP and its expiry time in the database
    $sql = "UPDATE patients SET otp = ?, otp_expires = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $otp, $otp_expires, $email);
    if ($stmt->execute() && sendOTP($email, $otp)) {
        echo "<script>alert('OTP sent to your email.');</script>";
    } else {
        echo "<script>alert('Failed to send OTP. Please try again.');</script>";
    }
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    $input_otp = $_POST['otp'];

    // Fetch stored OTP and expiry time from the database
    $sql = "SELECT otp, otp_expires FROM patients WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $stored_otp = $row['otp'];
        $otp_expires = $row['otp_expires'];

        // Check if OTP is valid and not expired
        if ($stored_otp == $input_otp && strtotime($otp_expires) > time()) {
            // OTP is valid, update the password
            $new_password = $_SESSION['change_password']['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "UPDATE patients SET password = ?, otp = NULL, otp_expires = NULL WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();

            echo "<script>alert('Password changed successfully.');</script>";
            header("location: user_home.php");
            exit();
        } else {
            // OTP is invalid or expired
            echo "<script>alert('Invalid or expired OTP. Please try again.');</script>";
        }
    } else {
        // User not found in the database
        header("location: patient_login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCAS - OTP Verification</title>
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
    </style>
</head>
<body>

<div class="form">
    <h2>OTP Verification</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="input-group">
            <input type="text" id="otp" name="otp" required>
            <label for="otp">Enter OTP</label>
        </div>
        <button class="submit-btn" type="submit" name="verify_otp">Verify OTP</button>
    </form>
    <br>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <button class="submit-btn" type="submit" name="send_otp">Resend OTP</button>
    </form>
</div>
</body>
</html>
