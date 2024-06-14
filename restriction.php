<?php
session_start();
include 'config.php';

$redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $access_code = $_POST['access_code'];
    $redirect_to = $_POST['redirect_to'];
    
    // Hash the user-provided access code
    $hashed_access_code = md5($access_code);
    
    // Prepare and bind
    $stmt = $conn->prepare("SELECT code FROM access_codes WHERE (role = 'admin' AND code = ?) OR (role = 'doctor' AND code = ?)");
    $stmt->bind_param("ss", $hashed_access_code, $hashed_access_code);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        header("Location: $redirect_to");
        exit();
    } else {
        $error_message = "Incorrect access code.";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Restriction Page</title>
    <style>
        body {
            background-image: url(image/15.jpg);
            font-family: sans-serif;
            background-size: cover;
        }
        .home-link {
            text-decoration: none;
            color: inherit;  /* Inherit the color from the parent */
        }

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

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <form class="form" method="POST" action="restriction.php">
			<td>
		<br>
		<a href="index.php" class="home-link">
			<i style="font-size:30px; color: white;" class="fa">&#xf015;</i>
		</a>
	</td>
            <h2>Enter Access Code</h2>
			
			<div class="input-group">
            <input type="password" name="access_code" placeholder="Access Code" required>		
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirect_to); ?>">
           </div>
		   <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
    <script>
        <?php
        if (isset($error_message)) {
            echo "alert('$error_message');";
        }
        ?>
    </script>
</body>
</html>
