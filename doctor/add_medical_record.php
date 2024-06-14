<?php
// Include config file
include '../config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all required fields are set
    $required_fields = ['patient_id', 'record_date', 'symptoms', 'vital_signs', 'examination_findings', 'treatment_plan', 'follow_up_instructions', 'notes'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (empty($missing_fields)) {
        // All required fields are set, proceed with database insertion
        $patient_id = $_POST['patient_id'];
        $record_date = $_POST['record_date'];
        $symptoms = $_POST['symptoms'];
        $vital_signs = $_POST['vital_signs'];
        $examination_findings = $_POST['examination_findings'];
        $treatment_plan = $_POST['treatment_plan'];
        $follow_up_instructions = $_POST['follow_up_instructions'];
        $notes = $_POST['notes'];

        $sql = "INSERT INTO medical_records (patient_id, record_date, symptoms, vital_signs, examination_findings, treatment_plan, follow_up_instructions, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $patient_id, $record_date, $symptoms, $vital_signs, $examination_findings, $treatment_plan, $follow_up_instructions, $notes);

        if ($stmt->execute()) {
            echo "<script>alert('Medical record added successfully.'); window.location.href = 'view_patient_record.php';</script>";
        } else {
            echo "<script>alert('Failed to add medical record.'); window.location.href = 'add_medical_record.php';</script>";
        }

        $stmt->close();
    } else {
        // Some required fields are missing
        echo "<script>alert('Please fill in all required fields: " . implode(", ", $missing_fields) . "'); window.location.href = 'add_medical_record.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medical Record</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Medical Record</h2>
        <form action="add_medical_record.php" method="post">
            <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient_id); ?>">
            <label for="record_date">Record Date</label>
            <input type="date" id="record_date" name="record_date" required>
            <label for="symptoms">Symptoms</label>
            <textarea id="symptoms" name="symptoms" rows="4" required></textarea>
            <label for="vital_signs">Vital Signs</label>
            <textarea id="vital_signs" name="vital_signs" rows="4" required></textarea>
            <label for="examination_findings">Examination Findings</label>
            <textarea id="examination_findings" name="examination_findings" rows="4" required></textarea>
            <label for="treatment_plan">Treatment Plan</label>
            <textarea id="treatment_plan" name="treatment_plan" rows="4" required></textarea>
            <label for="follow_up_instructions">Follow-Up Instructions</label>
            <textarea id="follow_up_instructions" name="follow_up_instructions" rows="4" required></textarea>
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="4" required></textarea>
            <button type="submit">Add Record</button>
        </form>
    </div>
</body>
</html>
