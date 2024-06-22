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

$patient_id = $_SESSION['patient_id']; // Retrieve the patient_id from the session


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $address_line1 = isset($_POST['address_line1']) ? $_POST['address_line1'] : "";
    $address_line2 = isset($_POST['address_line2']) ? $_POST['address_line2'] : "";
    $city = isset($_POST['city']) ? $_POST['city'] : "";
    $state = isset($_POST['state']) ? $_POST['state'] : "";
    $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : "";
	
 // Check if all required fields are filled
    if (empty($address_line1) || empty($address_line2) || empty($city)) {
        $error = "Please fill out all required fields.";
    } else {
        try {
            if(!empty($address_id)) {
                // Update existing address
                $sql = "UPDATE addresses SET address_line1 = ?, address_line2 = ?, city = ?, state = ?, postcode = ? WHERE id = ? AND patient_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssii", $address_line1, $address_line2, $city, $state, $postcode, $address_id, $patient_id);
            } else {
                // Insert new address
                $sql = "INSERT INTO addresses (address_line1, address_line2, city, state, postcode, patient_id) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $address_line1, $address_line2, $city, $state, $postcode, $patient_id);
            }

            // Execute the query
            $stmt->execute();

            // Set success message
            $success = "Address " . (!empty($address_id) ? "updated" : "added") . " successfully!";
        } catch (mysqli_sql_exception $e) {
            // Handle database errors
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle address deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $address_id = $_POST['address_id'];
        $sql = "DELETE FROM addresses WHERE address_id = ? AND patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $address_id, $patient_id);
        $stmt->execute();

        // Set success message
        $success = "Address deleted successfully!";
    } catch (mysqli_sql_exception $e) {
        // Handle database errors
        $error = "Error: " . $e->getMessage();
    }
}

// Check if there is a success message in the URL parameters
$success_message = isset($_GET['success']) ? $_GET['success'] : "";
// Initialize addresses array
$addresses = [];

// Fetch addresses for the logged-in patient
$addresses = [];
try {
    $sql = "SELECT * FROM addresses WHERE patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['patient_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $addresses = $result->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    $error = "Error fetching addresses: " . $e->getMessage();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>HCAS - PATIENT ADDRESS</title>
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
        HCAS - Patient Address
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
<!-- Display saved addresses -->
    <div class="profile-container">
        <div class="profile-info">
            <center><h2>Saved Addresses</h2></center>
            <?php if (!empty($addresses)): ?>
                <?php foreach ($addresses as $address): ?>
                    <ul>
                        <li>
                            <span><?php echo htmlspecialchars($address['address_line1']); ?></span>,
                            <span><?php echo htmlspecialchars($address['address_line2']); ?></span>,
                            <span><?php echo htmlspecialchars($address['city']); ?></span>,
                            <span><?php echo htmlspecialchars($address['state']); ?></span>,
                            <span><?php echo htmlspecialchars($address['postcode']); ?></span>
                            <a href="edit_address.php?address_id=<?php echo $address['address_id']; ?>">Edit</a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $address['address_id']; ?>)">Delete</a>
                        </li>
                    </ul>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No addresses found.</p>
            <?php endif; ?>
        </div>
    </div>


 <!-- Address form -->
    <div class="login-wrapper"> 
        <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2><?php echo !empty($_POST['address_id']) ? 'Edit Address' : 'Add Address'; ?></h2>
            <?php if(isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if(isset($success)): ?>
                <script>
                    // Display success message as a popup alert
                    alert("<?php echo $success; ?>");
                </script>
            <?php endif; ?>
    
        <label for="address_line1">Address Line 1<span style="color: red;">*</span></label>
            <div class="input-group">    
                <input type="text" id="address_line1" name="address_line1" required>
            </div>
            
            <label for="address_line2">Address Line 2</label>
            <div class="input-group">  
                <input type="text" id="address_line2" name="address_line2" required>
            </div>
            
            <label for="city">City<span style="color: red;">*</span></label>
            <div class="input-group">  
                <input type="text" id="city" name="city" required>
            </div>
            
            <label for="state">State<span style="color: red;">*</span></label>
            <div class="input-group">         
                <select id="state" name="state">
					<option value="">Select State (Optional)</option>
					<option value="Johor">Johor</option>
					<option value="Kedah">Kedah</option>
					<option value="Kelantan">Kelantan</option>
					<option value="Melaka">Melaka</option>
					<option value="Negeri Sembilan">Negeri Sembilan</option>
					<option value="Pahang">Pahang</option>
					<option value="Perak">Perak</option>
					<option value="Perlis">Perlis</option>
					<option value="Pulau Pinang">Pulau Pinang</option>
					<option value="Sabah">Sabah</option>
					<option value="Sarawak">Sarawak</option>
					<option value="Selangor">Selangor</option>
					<option value="Terengganu">Terengganu</option>
					<option value="WP Kuala Lumpur">WP Kuala Lumpur</option>
					<option value="WP Labuan">WP Labuan</option>
					<option value="WP Putrajaya">WP Putrajaya</option>
            </select>
            </div>
            
            <label for="postcode">Postcode<span style="color: red;">*</span></label>
            <div class="input-group"> 
                <input type="text" id="postcode" name="postcode">
            </div>

            <button type="submit" class="submit-btn"><?php echo !empty($_POST['address_id']) ? 'Update Address' : 'Save Address'; ?></button>
        </form>
    </div>

</body>
</html>
 <!-- JavaScript -->
    <script>
        function confirmDelete(addressId) {
            if (confirm("Are you sure you want to delete this address?")) {
                // AJAX request to delete the address
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_address.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Handle the response if needed
                        alert(xhr.responseText);
                        // Reload the page or update the address list
                        location.reload(); // This will reload the page to reflect changes
                    }
                };
                xhr.send("address_id=" + addressId);
            }
        }
    </script>

</body>
</html>
