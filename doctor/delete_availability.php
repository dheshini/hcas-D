<?php
session_start();

// Include config file
include '../config.php';

// Check if availability ID is provided in the URL
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Prepare a DELETE statement for the doctor_availability_services table
    $sql_delete_services = "DELETE FROM doctor_availability_services WHERE availability_id = ?";
    $stmt_delete_services = $conn->prepare($sql_delete_services);

    // Check if the DELETE statement was prepared successfully
    if ($stmt_delete_services) {
        // Bind the availability_id parameter and execute the statement
        $stmt_delete_services->bind_param("i", $availability_id);
        
        // Set parameters and execute the statement
        $availability_id = $_GET["id"];
        $stmt_delete_services->execute();
        
        // Close the statement
        $stmt_delete_services->close();
    } else {
        // Handle the case where the statement preparation failed
        $error_message = "Error: Unable to prepare DELETE statement for doctor_availability_services.";
    }

    // Prepare a DELETE statement for the doctor_availability table
    $sql = "DELETE FROM doctor_availability WHERE availability_id = ?";
    if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Redirect to view_availability page
            header("location: view_availability.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();
} else{
    // Check existence of availability ID parameter
    if(empty(trim($_GET["id"]))){
        // If URL doesn't contain availability ID parameter, redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
