<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $date = $_POST['date'];
    $empName = $_POST['emp_name'];
    $status = $_POST['status'];
    
    // Get employee ID based on the selected employee name
    $empIdQuery = "SELECT id FROM emp WHERE name = ?";
    
    if ($stmt = $conn->prepare($empIdQuery)) {
        $stmt->bind_param("s", $empName);
        $stmt->execute();
        $stmt->bind_result($empId);
        $stmt->fetch();
        $stmt->close();
        
        // Check if an attendance record for the same employee and date exists
        $atdCheckQuery = "SELECT id FROM atd WHERE emp_id = ? AND date = ?";
        if ($stmt = $conn->prepare($atdCheckQuery)) {
            $stmt->bind_param("is", $empId, $date);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                // Update existing attendance record
                $updateQuery = "UPDATE atd SET status = ? WHERE emp_id = ? AND date = ?";
                if ($stmt = $conn->prepare($updateQuery)) {
                    $stmt->bind_param("sis", $status, $empId, $date);
                    if ($stmt->execute()) {
                        // Success: Redirect to the attendance form or show success message
                        header("Location: ../atd.php");
                        exit();
                    } else {
                        // Handle update error if needed
                        echo "Error updating attendance.";
                    }
                    $stmt->close();
                } else {
                    // Handle database error if needed
                    echo "Database error.";
                }
            } else {
                // Insert new attendance record
                $insertQuery = "INSERT INTO atd (emp_id, date, status) VALUES (?, ?, ?)";
                if ($stmt = $conn->prepare($insertQuery)) {
                    $stmt->bind_param("iss", $empId, $date, $status);
                    if ($stmt->execute()) {
                        // Success: Redirect to the attendance form or show success message
                        header("Location: ../atd.php");
                        exit();
                    } else {
                        // Handle insert error if needed
                        echo "Error inserting attendance.";
                    }
                    $stmt->close();
                } else {
                    // Handle database error if needed
                    echo "Database error.";
                }
            }
        } else {
            // Handle error if needed
            echo "Database error.";
        }
    } else {
        // Handle error if needed
    }
    
    $conn->close();
} else {
    // Redirect if accessed directly
    header("Location: ../atd.php");
    exit();
}
?>
