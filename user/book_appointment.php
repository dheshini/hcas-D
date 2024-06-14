<?php
session_start();
include '../config.php'; // Ensure this file contains your database connection details
include('../session.php');

// Retrieve list of services available on the selected date
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["date"])) {
    // Retrieve date from GET parameter
    $date = $_GET['date'];

    // Query to fetch available services for the given date
    $sql_available_services = "SELECT s.service_id, s.name, s.description
                           FROM services s
                           WHERE EXISTS (
                               SELECT 1
                               FROM doctor_availability da
                               WHERE da.service_id = s.service_id
                               AND da.date = ?
                           )";

    $stmt_available_services = $conn->prepare($sql_available_services);
    if (!$stmt_available_services) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt_available_services->bind_param("s", $date);
    $stmt_available_services->execute();
    $result_available_services = $stmt_available_services->get_result();
}

// Calculate current date and maximum future date
$current_date = date("Y-m-d");
$max_date = date("Y-m-d", strtotime("+6 months")); // Set maximum future date to 6 months from today

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-J1Xj5oNsRt2jLXp8u+7j3Zt5OmuIO9+1eyQWOKb9vHQQZGzOvQ7tn2sAcG8g3EjlMIvc8aGozT4vxeBb0G/mDg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>HCAS - PATIENT BOOKING</title>
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

		#notification-bell {
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

       .book-btn {
    background: #ff652f;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
	   font-size: 1rem;}


        .date-selection {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            text-align: center;
        }

        .date-selection form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date-selection label {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .date-selection input {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 1rem;
            border: none;
        }

        .date-selection button {
            background: #ff652f;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1rem;
        }

        .service-list {
            background: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 10px;
            color: #fff;
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
        }

        .service-list h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .service-list ul {
            list-style: none;
            padding: 0;
        }

        .service-list ul li {
            background: rgba(0, 0, 0, 0.5);
            margin: 10px 0;
            padding: 20px;
            border-radius: 5px;
            font-size: 1.2rem;
        }

        .service-list ul ul li {
            font-size: 1rem;
            margin: 5px 0;
            padding: 10px;
        }
		
		.book-btn {
            background: #ff652f;
            color: #fff;
            border: none;
            padding: 5px 10px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <header>
        HCAS - Patient View Available Doctors
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
    <div id="sidebar">
        <a href="logout.php">SignOut<a>
		<h3>Today's News or Updates</h3>
        <div id="news-content"></div>
    </div>
	
    <div class="date-selection">
        <h1>Available Services and Doctors</h1>

        <!-- Form to select date to view available services -->
        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" required min="<?php echo $current_date; ?>" max="<?php echo $max_date; ?>">
            <button type="submit">View Available Services</button>
        </form>
    </div>

   <!-- Display available services -->
<div class="service-list">
    <?php if (isset($result_available_services) && $result_available_services->num_rows > 0): ?>
        <h2>Available Services on <?php echo $date; ?>:</h2>
        <ul>
            <?php while ($row_service = $result_available_services->fetch_assoc()): ?>
			
                <li>
                    <strong><?php echo $row_service["name"]; ?></strong>
                    <p> - <?php echo $row_service["description"]; ?></p> <!-- Display service description -->
                    <ul>
                        <!-- Retrieve available doctors for the service on the selected date -->
                        <?php
                        $service_id = $row_service["service_id"];
                        $sql_available_doctors = "SELECT d.doctor_id, d.username, da.start_time, da.end_time
                                                  FROM doctor_availability da
                                                  INNER JOIN doctors d ON da.doctor_id = d.doctor_id
                                                  WHERE da.service_id = ? AND da.date = ?";
                        $stmt_available_doctors = $conn->prepare($sql_available_doctors);
                        if (!$stmt_available_doctors) {
                            die("Error preparing statement: " . $conn->error);
                        }
                        $stmt_available_doctors->bind_param("is", $service_id, $date);
                        $stmt_available_doctors->execute();
                        $result_available_doctors = $stmt_available_doctors->get_result();
                        ?>
                        <?php while ($row_doctor = $result_available_doctors->fetch_assoc()): ?>
                            <li>
                                Doctor: <?php echo $row_doctor["username"]; ?>
                                ( Time: <?php echo $row_doctor["start_time"]; ?> - <?php echo $row_doctor["end_time"]; ?> )
                                <form method="post" action="booking.php">
                                    <input type="hidden" name="doctor" value="<?php echo $row_doctor["doctor_id"]; ?>">
                                    <input type="hidden" name="service" value="<?php echo $service_id; ?>">
                                    <input type="hidden" name="date" value="<?php echo $date; ?>">
                                    <br><input type="submit" value="Book" class="book-btn">
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["date"])): ?>
        <p>No services available on the selected date.</p>
    <?php endif; ?>
</div>

    <script>
        function validateDate() {
            const selectedDate = new Date(document.getElementById('date').value);
            const currentDate = new Date();
            const currentHour = currentDate.getHours();
            const currentMinute = currentDate.getMinutes();
            
            // Reset time to midnight for accurate comparison
            currentDate.setHours(0, 0, 0, 0);

            // If current time is past 12:00 PM, disallow booking for the current date
            if (currentHour > 12 || (currentHour === 12 && currentMinute > 0)) {
                if (selectedDate.toDateString() === currentDate.toDateString()) {
                    alert('Appointments cannot be made for the current date after 12:00 PM.');
                    document.getElementById('date').value = '';
                    return;
                }
            }

            // If selected date is before the current date, disallow booking
            if (selectedDate < currentDate) {
                alert('Please select a future date.');
                document.getElementById('date').value = '';
            }
        }
    </script>
	
    <div id="menu-icon">&#9776;</div>
    <script>
        document.getElementById('menu-icon').addEventListener('click', function () {
            document.getElementById('sidebar').style.width = (document.getElementById('sidebar').style.width === '250px') ? '0' : '250px';
        });
    </script>
	
    <script>
        // Function to update the news content with the current date and time
        function updateNews() {
            const currentDate = new Date();
            const formattedDate = currentDate.toLocaleString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric'
            });
            document.getElementById('news-content').innerHTML = `<p>${formattedDate}</p>`;
        }

        // Initial call to update the news content
        updateNews();

        // Function to update the news content every second
        setInterval(updateNews, 1000);
    </script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>
