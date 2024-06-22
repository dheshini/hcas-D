<?php
session_start();
include '../config.php';
include '../session.php';

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
// Function to insert feedback into the database
function insertFeedback($email, $type, $message, $conn) {
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO feedback (patient_id, type, message) VALUES ((SELECT patient_id FROM patients WHERE email = ?), ?, ?)");
    $stmt->bind_param("sss", $email, $type, $message);

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        return true; // Feedback inserted successfully
    } else {
        return false; // Error inserting feedback
    }

    // Close statement
    $stmt->close();
}

// Function to delete feedback from the database
function deleteFeedback($feedback_id, $conn) {
    // Prepare SQL statement
    $stmt = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
    $stmt->bind_param("i", $feedback_id);

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        return true; // Feedback deleted successfully
    } else {
        return false; // Error deleting feedback
    }

    // Close statement
    $stmt->close();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_feedback'])) {
        // Check if the user is logged in
        if (!isset($_SESSION['email'])) {
            echo "<script>alert('Please log in to delete feedback.');</script>";
            exit; // Stop further execution
        }

         // Check if feedback_id is set
        if (isset($_POST['feedback_id'])) {
            // Retrieve feedback ID
            $feedback_id = $_POST['feedback_id'];

            // Delete feedback from the database
            $deleted = deleteFeedback($feedback_id, $conn);

            if ($deleted) {
                echo "<script>alert('Feedback deleted successfully.');</script>";
            } else {
                echo "<script>alert('Error deleting feedback.');</script>";
            }
        } else {
            echo "<script>alert('Please select feedback to delete.');</script>";
        }
    } else {
        // Retrieve form data
        $email = $_SESSION['email'];
        $type = $_POST["type"];
        $message = $_POST["message"];

        // Insert feedback into the database
        $inserted = insertFeedback($email, $type, $message, $conn);

        if ($inserted) {
            echo "<script>alert('Feedback submitted successfully.');</script>";
        } else {
            echo "<script>alert('Error submitting feedback.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>HCAS - Patient Feedback</title>
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
            border-radius: 50%;
            padding: 5px 10px;
            position: absolute;
            top: -10px;
            right: -10px;
        }
.form-wrapper {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px;
        }
 
        .form {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
            margin: 20px;
        }

        .form h2 {
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 2rem;
        }

     .form .input-group {
    position: relative;
    margin-bottom: 30px;
}

.form .input-group label {
    position: absolute;
    top: 10px;
    left: 0;
    padding: 10px 0;
    font-size: 1rem;
    pointer-events: none;
    transition: 0.3s ease-out;
    color: rgba(255, 255, 255, 0.7);
}

.form .input-group input,
.form .input-group select {
    width: 100%;
    padding: 10px 0;
    font-size: 1rem;
    letter-spacing: 1px;
    margin-top: 15px;
    border: none;
    border-bottom: 1px solid #fff;
    outline: none;
    background-color: transparent;
    color: white;
}

.form .input-group input:focus,
.form .input-group select:focus {
    border-bottom-color: #ff652f;
}

.form .input-group input:valid + label,
.form .input-group select:valid + label,
.form .input-group input:focus + label,
.form .input-group select:focus + label {
    transform: translateY(-20px);
    font-size: 0.8rem;
    color: #ff652f;
}

.form .submit-btn {
    display: block;
    margin: auto;
    margin-top: 30px;
    border: none;
    outline: none;
    background: #ff652f;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.form .submit-btn:hover {
    background: #ff906f;
}

.feedback-table {
    width: 100%;
    max-width: 600px;
    background: rgba(0, 0, 0, 0.7);
    padding: 20px;
    border-radius: 10px;
    color: #fff;
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
}

.feedback-table table {
    width: 100%;
    border-collapse: collapse;
}

.feedback-table th,
.feedback-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.feedback-table th {
    background-color: #ff652f;
    color: white;
}

.feedback-table td {
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
}

.feedback-table tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.1);
}

.feedback-table tr:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Styling for select dropdown */
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
    color: white; /* Text color */
    appearance: none; /* Remove default arrow icon */
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer; /* Show pointer cursor */
}

.form .input-group select option {
    background-color: rgba(0, 0, 0, 0.7); /* Background color for options */
    color: white; /* Text color for options */
}

.form .input-group select:focus {
    border-bottom-color: #ff652f; /* Highlight border color when focused */
}

/* Style for select dropdown arrow */
.form .input-group::after {
    content: '\25BC'; /* Unicode character for down arrow */
    position: absolute;
    top: 13px;
    right: 15px;
    font-size: 14px;
    color: white;
    pointer-events: none; /* Avoid click events on arrow */
}

/* Adjust hover and focus states for options */
.form .input-group select option:hover,
.form .input-group select option:focus {
    background-color: #ff652f; /* Background color on hover/focus */
    color: white; /* Text color on hover/focus */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form {
        padding: 20px;
    }

    .form .submit-btn {
        width: 100%;
    }

    .feedback-table {
        padding: 10px;
    }
} 
    </style>
</head>

<body>
    <header>
        HCAS - Patient Feedback
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

     <div class="form-wrapper">
        <div class="form feedback-table">
            <h2>Your Feedback and Replies</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <table border='1' align='center' cellpadding='10'>
                    <tr>
                        <th>Select</th>
                        <th>Feedback ID</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Replied Message</th>
                        <th>Created At</th>
                    </tr>
                    <?php
                    // Retrieve feedback and replies from the database
                    $email = $_SESSION['email'];
                    $sql = "SELECT f.feedback_id, f.type, f.message, f.created_at, fr.reply_message 
                            FROM feedback f
                            LEFT JOIN feedback_replies fr ON f.feedback_id = fr.feedback_id
                            WHERE f.patient_id = (SELECT patient_id FROM patients WHERE email = '$email')";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        // Output data of each row
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td><input type='radio' name='feedback_id' value='" . $row["feedback_id"] . "'></td>";
                            echo "<td>" . $row["feedback_id"] . "</td>";
                            echo "<td>" . $row["type"] . "</td>";
                            echo "<td>" . $row["message"] . "</td>";
                            echo "<td>" . ($row["reply_message"] ? $row["reply_message"] : "No reply yet") . "</td>";
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No feedback found.</td></tr>";
                    }
                    ?>
                </table>
                <input class="submit-btn" type="submit" name="delete_feedback" value="Delete">
            </form>
        </div>


        <div class="form">
            <form method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h2>Submit Feedback</h2>
                <label for="type">Type</label><br>
                <div class="input-group">
                    <select class="input-group select" name="type">
                        <option value="feedback">Feedback</option>
                        <option value="inquiry">Inquiry</option>
                        <option value="complaint">Complaint</option>
                    </select>
                </div>
                <label for="message">Message</label>
                <div class="input-group">
                    <input name="message" required />
                </div>
                <input class="submit-btn" type="submit" value="Submit">
            </form>
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