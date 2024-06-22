<?php
// Start the session
session_start();
include '../config.php'; // Include the database connection script
include('../session.php'); // Include session management script

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

// Get the patient ID from the session
$patient_id = $_SESSION['patient_id'];

// Fetch medical records for the logged-in patient
$sql_medical = "SELECT * FROM medical_records WHERE patient_id = ? ORDER BY record_date DESC";
$stmt_medical = $conn->prepare($sql_medical);
$stmt_medical->bind_param("i", $patient_id);
$stmt_medical->execute();
$result_medical = $stmt_medical->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>HCAS - PATIENT VIEW MEDICAL RECORDS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-J1Xj5oNsRt2jLXp8u+7j3Zt5OmuIO9+1eyQWOKb9vHQQZGzOvQ7tn2sAcG8g3EjlMIvc8aGozT4vxeBb0G/mDg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Times;
                    margin: 0;
                    padding: 8;
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
                    border-radius: 4px;
                }

                nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        nav i {
            color: #ff652f;
            margin-right: 8px;
        }


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
                    width: 100vw;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .form {
                    position: relative;
                    width: 100%;
                    max-width: 600px;
                    padding: 50px 50px 50px;
                    background: rgba(0, 0, 0, 0.7);
                    border-radius: 10px;
                    color: #fff;
                    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
					color: black;
                }
.medical-record {
    margin-bottom: 20px;
    padding: 10px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
		color: black;
}
 h6{
	 font-size:15px;
     text-align: center;
     letter-spacing: 1px;
       margin-bottom: 2rem;
    color: white;}
	
.medical-record p {
    margin: 5px 0;
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

                h2 {
                    text-align: center;
                    letter-spacing: 1px;
                    margin-bottom: 2rem;
                    color: black;
                }
 h1 {
            text-align: center;
            color: white;
            font-size: 2rem;
            margin-top: 20px;
        }
               .input-group {
                    position: relative;
                }

               .input-group input {
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

                .input-group label {
                    position: absolute;
                    top: 0;
                    left: 0;
                    padding: 10px 0;
                    font-size: 1rem;
                    pointer-events: none;
                    transition: 0.3s ease-out;
                }

              .input-group input:focus + label,
                .input-group input:valid + label {
                    transform: translateY(-18px);
                    color: white;
                    font-size: 0.8rem;
                }

                .input-group select {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    background-color: #fff;
                    cursor: pointer;
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
  select.submit-btn {
        font-size: 1rem;
        border: none;
        border-radius: 5px;
        background-color: #fff; /* Background color */
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Optional: Shadow for depth */
        display: block;
        margin-left: auto; /* Optional: Margin between select and button */
		padding: 10px 20px;
    }

                .patient-info {
                    background: rgba(255, 255, 255, 0.8);
                    padding: 20px;
                    border-radius: 10px;
                    max-width: 800px;
                    margin: 20px auto;
                }

                .patient-info h1, .patient-info h2 {
                    color: #333;
                }

                .patient-info p {
                    font-size: 1rem;
                    color: #555;
                    margin: 10px 0;
                }

            </style>
        </head>
<body>
    <header>
        HCAS - Patient View Medical Records
    </header>

   <nav>
        <a href="user_home.php"><i class="fas fa-home"></i>Home</a>
        <a href="book_appointment.php"><i class="fas fa-calendar-plus"></i>Book Appointment</a>
        <a href="booking_history.php"><i class="fas fa-history"></i>View Appointments</a>
        <a href="view_medical_records.php"><i class="fas fa-file-medical"></i>View Medical Records</a>
        <a href="profile.php"><i class="fas fa-user"></i>Account</a>
        <a href="contact_us.php"><i class="fas fa-envelope"></i>Contact Us</a>
	</nav>

    <div id="notification-bell">
        <span id="notification-count">5</span>
    </div>
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

</body>
</html>



<br><br><br>
    <div class="login-wrapper">
	<div class="form">
        <h1>My Medical Records</h1>
		             <hr>
<br>
        <?php
        if ($result_medical->num_rows > 0) {
		echo "<h6>Below are your medical records as recorded by your doctor:</h6>";
            while ($row = $result_medical->fetch_assoc()) {
                echo "<div class='medical-record'>";
                echo "<h2>Record Date: " . htmlspecialchars($row['record_date']) . "</h2>";
                echo "<p><strong>Symptoms:</strong> " . htmlspecialchars($row['symptoms']) . "</p>";
                echo "<p><strong>Vital Signs:</strong> " . htmlspecialchars($row['vital_signs']) . "</p>";
                echo "<p><strong>Examination Findings:</strong> " . htmlspecialchars($row['examination_findings']) . "</p>";
                echo "<p><strong>Treatment Plan:</strong> " . htmlspecialchars($row['treatment_plan']) . "</p>";
                echo "<p><strong>Follow-up Instructions:</strong> " . htmlspecialchars($row['follow_up_instructions']) . "</p>";
                echo "<p><strong>Additional Notes:</strong> " . htmlspecialchars($row['notes']) . "</p>";
                echo "</div>";
                echo "<hr>";
            }
        } else {
            echo "<p>No medical records found.</p>";
        }
        ?>
    </div>
	</div>
</body>
</html>

<?php
$stmt_medical->close();
$conn->close();
?>
