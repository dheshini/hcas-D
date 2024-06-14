<?php
// Include config file
include '../config.php';

// Check if patient ID is set and valid
if (isset($_POST['selected_patient_id'])) {
    $patient_id = $_POST['selected_patient_id'];

    // Query to fetch patient information
    $sql = "SELECT p.*, a.*, pr.* FROM patients p
            LEFT JOIN addresses a ON p.patient_id = a.patient_id
            LEFT JOIN patient_records pr ON p.patient_id = pr.patient_id
            WHERE p.patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient_data = $result->fetch_assoc();
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Patient Information</title>
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
                    height: 80vh;
                    width: 100vw;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .form {
                    position: relative;
                    width: 150%;
                    max-width: 1300px;
                    padding: 50px 50px 50px;
                    background: rgba(0, 0, 0, 0.7);
                    border-radius: 10px;
                    color: #fff;
                    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
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
 h1 {
            text-align: center;
            color: white;
            font-size: 2rem;
            margin-top: 20px;
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
                HCAS Appointment - View Patient Information
            </header>

            <div id="notification-bell">
                <span id="notification-count">5</span>
            </div>

            <nav>
                <a href="doctor_home.php">Home</a>
                <a href="view_appointment.php">View Appointment</a>
                <a href="message.php">Next Appointment</a>
                <a href="add_availability.php">Add Availability</a>
                <a href="profile.php">My Profile</a>
            </nav>

            <div id="sidebar">
                <a href="logout.php">Sign Out</a>
                <h3>Today's News or Updates</h3>
                <div id="news-content"></div>
            </div>

            <div class="patient-info">
                <h1>Patient Information</h1>
                <h2>Personal Details</h2>
                <p>Username: <?php echo htmlspecialchars($patient_data['username']); ?></p>
                <p>Email: <?php echo htmlspecialchars($patient_data['email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($patient_data['phone']); ?></p>
                <p>Date of Birth: <?php echo htmlspecialchars($patient_data['date_of_birth']); ?></p>

                <h2>Address</h2>
                <p>Address Line 1: <?php echo htmlspecialchars($patient_data['address_line1']); ?></p>
                <p>Address Line 2: <?php echo htmlspecialchars($patient_data['address_line2']); ?></p>
                <p>City: <?php echo htmlspecialchars($patient_data['city']); ?></p>
                <p>State: <?php echo htmlspecialchars($patient_data['state']); ?></p>
                <p>Postcode: <?php echo htmlspecialchars($patient_data['postcode']); ?></p>

                <h2>Medical Records</h2>
                <?php if (!empty($patient_data['medical_surgical_family_history'])) : ?>
                    <p>Medical and Surgical Family History: <?php echo htmlspecialchars($patient_data['medical_surgical_family_history']); ?></p>
                <?php endif; ?>
                <p>Surgery Year: <?php echo htmlspecialchars($patient_data['surgery_year']); ?></p>
                <?php if (!empty($patient_data['allergies'])) : ?>
                                        <p>Allergies: <?php echo htmlspecialchars($patient_data['allergies']); ?></p>
                <?php endif; ?>
                <?php if (!empty($patient_data['past_medical_history'])) : ?>
                    <p>Past Medical History: <?php echo htmlspecialchars($patient_data['past_medical_history']); ?></p>
                <?php endif; ?>
                <?php if (!empty($patient_data['clinical_summary'])) : ?>
                    <p>Clinical Summary: <?php echo htmlspecialchars($patient_data['clinical_summary']); ?></p>
                <?php endif; ?>
                <p>Allergies Specify: <?php echo htmlspecialchars($patient_data['allergies_specify']); ?></p>
                
                <!-- Back button -->
                <button class="submit-btn" onclick="goBack()">Back</button>

                <script>
                    function goBack() {
                        window.history.back();
                    }
                </script>
            </div>

            <div id="menu-icon">&#9776;</div>
            <script>
                function checkSelection() {
                    const selectedPatient = document.querySelector('input[name="selected_patient_id"]:checked');
                    if (!selectedPatient) {
                        alert('Please select a patient');
                    } else {
                        document.getElementById('appointmentForm').submit();
                    }
                }

                document.getElementById('menu-icon').addEventListener('click', function () {
                    document.getElementById('sidebar').style.width = (document.getElementById('sidebar').style.width === '250px') ? '0' : '250px';
                });

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
            <?php
        } else {
            echo "<p>No patient found with the provided ID.</p>";
        }

        // Close statement and database connection
        $stmt->close();
        $conn->close();
    } else {
        // Redirect user if patient ID is not set
        header("Location: view_appointment.php");
        exit();
    }
    ?>
</body>
</html>
