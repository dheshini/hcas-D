<?php
session_start();
include '../config.php';
include('../session.php');
// Check if the user is logged in, if not redirect to login page
if(!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

// Check if address_id is provided in the URL
if(!isset($_GET['address_id'])) {
    header("Location: address.php");
    exit();
}

$patient_id = $_SESSION['patient_id']; // Retrieve the patient_id from the session
$address_id = $_GET['address_id']; // Retrieve the address_id from the URL parameter

// Fetch the address details
$address = [];
try {
    $sql = "SELECT * FROM addresses WHERE address_id = ? AND patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $address_id, $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $address = $result->fetch_assoc();
    if(!$address) {
        // Redirect if address not found for the user
        header("Location: address.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    $error = "Error fetching address: " . $e->getMessage();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $address_line1 = $_POST['address_line1'];
    $address_line2 = $_POST['address_line2'];
    $city = $_POST['city'];
    $state = isset($_POST['state']) ? $_POST['state'] : "";
    $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : "";

    // Check if all required fields are filled
    if (empty($address_line1) || empty($address_line2) || empty($city)) {
        $error = "Please fill out all required fields.";
    } else {
        try {
            // Update the address
            $sql = "UPDATE addresses SET address_line1 = ?, address_line2 = ?, city = ?, state = ?, postcode = ? WHERE address_id = ? AND patient_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssii", $address_line1, $address_line2, $city, $state, $postcode, $address_id, $patient_id);

            // Execute the query
            $stmt->execute();

            // Set success message
            $success = "Address updated successfully!";
            
            // Redirect to address.php after 3 seconds
            echo "<script>
                    alert('$success');
                    setTimeout(function(){
                        window.location.href = 'address.php';
                    }, 1000);
                  </script>";
        } catch (mysqli_sql_exception $e) {
            // Handle database errors
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!-- HTML code for edit address form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCAS - PATIENT EDIT ADDRESS </title>
	<style>
          body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
		h3{  text-align: center;
            font-size: 36px;
			font-family: 'Segoe UI', Times, Geneva, Verdana, san}
	/* CSS styles for form */
	
  .login-wrapper {
  height: 85vh;
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

        .form .book-btn {
            display: block;
            margin-left: auto;
            border: none;
            outline: none;
            background: #ff652f;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 5px;
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
        HCAS - Patient Update Address
    </header>

    <div id="notification-bell">
        <span id="notification-count">5</span>
    </div>

    <nav>
        <a href="user_home.php">Home</a>
        <a href="book_appointment.php">Book Appointment</a>
        <a href="booking_history.php">View Appointments</a>
        <a href="profile.php">Account</a>
		<a href="contact_us.php">Contact Us</a>
    </nav>

    <div class="profile-buttons">
        <a href="settings.php">Settings</a>
        <a href="address.php">Address</a>
        <a href="medical_reports.php">Medical Reports</a>
        <a href="inbox.php">Inbox</a>
    </div>
	

     <!-- Address form -->
    <div class="login-wrapper"> 
        <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?address_id=' . $address_id; ?>">
            <h2>Edit Address</h2>
            <?php if(isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
    
            <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
            
            <label for="address_line1">Address Line 1<span style="color: red;">*</span></label>
            <div class="input-group">    
                <input type="text" id="address_line1" name="address_line1" value="<?php echo htmlspecialchars($address['address_line1']); ?>" required>
            </div>
            
            <label for="address_line2">Address Line 2</label>
            <div class="input-group">  
                <input type="text" id="address_line2" name="address_line2" value="<?php echo htmlspecialchars($address['address_line2']); ?>" required>
            </div>
            
            <label for="city">City<span style="color: red;">*</span></label>
            <div class="input-group">  
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($address['city']); ?>" required>
            </div>
            
            <label for="state">State<span style="color: red;">*</span></label>
            <div class="input-group">         
                <select id="state" name="state" required>
                    <option value="">Select State (Optional)</option>
                    <option value="Johor" <?php if ($address['state'] == 'Johor') echo 'selected'; ?>>Johor</option>
                    <option value="Kedah" <?php if ($address['state'] == 'Kedah') echo 'selected'; ?>>Kedah</option>
                    <option value="Kelantan" <?php if ($address['state'] == 'Kelantan') echo 'selected'; ?>>Kelantan</option>
                    <option value="Melaka" <?php if ($address['state'] == 'Melaka') echo 'selected'; ?>>Melaka</option>
                    <option value="Negeri Sembilan" <?php if ($address['state'] == 'Negeri Sembilan') echo 'selected'; ?>>Negeri Sembilan</option>
                    <option value="Pahang" <?php if ($address['state'] == 'Pahang') echo 'selected'; ?>>Pahang</option>
                    <option value="Perak" <?php if ($address['state'] == 'Perak') echo 'selected'; ?>>Perak</option>
                    <option value="Perlis" <?php if ($address['state'] == 'Perlis') echo 'selected'; ?>>Perlis</option>
                    <option value="Pulau Pinang" <?php if ($address['state'] == 'Pulau Pinang') echo 'selected'; ?>>Pulau Pinang</option>
                    <option value="Sabah" <?php if ($address['state'] == 'Sabah') echo 'selected'; ?>>Sabah</option>
                    <option value="Sarawak" <?php if ($address['state'] == 'Sarawak') echo 'selected'; ?>>Sarawak</option>
                    <option value="Selangor" <?php if ($address['state'] == 'Selangor') echo 'selected'; ?>>Selangor</option>
                    <option value="Terengganu" <?php if ($address['state'] == 'Terengganu') echo 'selected'; ?>>Terengganu</option>
                    <option value="WP Kuala Lumpur" <?php if ($address['state'] == 'WP Kuala Lumpur') echo 'selected'; ?>>WP Kuala Lumpur</option>
                    <option value="WP Labuan" <?php if ($address['state'] == 'WP Labuan') echo 'selected'; ?>>WP Labuan</option>
                    <option value="WP Putrajaya" <?php if ($address['state'] == 'WP Putrajaya') echo 'selected'; ?>>WP Putrajaya</option>
                </select>
            </div>
            
            <label for="postcode">Postcode<span style="color: red;">*</span></label>
            <div class="input-group"> 
                <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($address['postcode']); ?>" required>
            </div>
            <button type="submit" class="submit-btn">Update Address</button>
        </form>
    </div>
	</body>
</html>