<?php
session_start();

// Include config file
include '../config.php';

// Check if the doctor is logged in and retrieve doctor ID from session
$doctor_id = null;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT doctor_id FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $doctor_id = $row['doctor_id'];
    }
    $stmt->close();
}

// Fetch doctor availability data with service names
$sql = "SELECT da.availability_id, da.date, da.start_time, da.end_time, s.name AS service_name 
        FROM doctor_availability da 
        JOIN services s ON da.service_id = s.service_id 
        WHERE da.doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>View Availability</title>
    <style>
          body{font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Times;
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
			border-radius:4px;
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
	
  .login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
}
.form {
  position: relative;
  width: 100%;
  max-width: 900px;
  padding: 40px 40px 40px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 10px;
  color: #fff;
  box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
  font-family: sans-serif;
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
  font-size: 1rem;
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
		
		  table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background-color: rgba(0, 0, 0, 0.7);
			
        }

        th,
        td {
            border: 1px solid #fff;
            padding: 10px;
            text-align: left;
            color: #fff;
			
        }

        th {
            background-color: #ff652f;
        }

        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
/* Style the dustbin icon */
.trash-icon {
    color: #ff0000; /* Adjust the color of the icon */
    font-size: 20px; /* Adjust the size of the icon as needed */
}

/* Style the link */
.trash-icon-link {
    text-decoration: none; /* Remove underline */
    color: inherit; /* Inherit color from parent */
}
	 </style>
</head>

<body>
    <header>
        HCAS Appointment - Add Availability
    </header>

    <nav>
        <a href="doctor_home.php">Home</a>
        <a href="view_appointment.php">View Appointment</a>
        <a href="message.php">Next Appointment</a>
		<a href="add_availability.php">Add Availability</a>
        <a href="profile.php">My Profile</a>
    </nav>
	
	<br>
	<br>
	<br>
 <div class="login-wrapper">
  <div class="form">
  <h2>View Availability</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Service</th>
                        <th>Action</th> <!-- New column for action -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <?php
                            // Convert start_time and end_time to 12-hour format with AM/PM
                            $start_time = date("g:i A", strtotime($row['start_time']));
                            $end_time = date("g:i A", strtotime($row['end_time']));
                        ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $start_time; ?></td>
                            <td><?php echo $end_time; ?></td>
                            <td><?php echo $row['service_name']; ?></td> <!-- Display service name -->
<td>
    <!-- Dustbin icon with a link to delete_availability.php -->
    <a href="delete_availability.php?id=<?php echo $row['availability_id']; ?>" class="trash-icon-link">
        <i class="fas fa-trash-alt trash-icon"></i> <!-- Add the fas fa-trash-alt class for the icon -->
    </a>
</td>                  
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>