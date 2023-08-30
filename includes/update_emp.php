<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    echo "Script is being executed!<br>";
    $empId = $_POST["id"];
    $empName = $_POST["name"];
    $salary = $_POST["salary"];
    
    include 'db_connection.php';
    
    echo "Employee ID: $empId<br>";
    echo "Employee Name: $empName<br>";
    echo "salary: $salary<br>";
    
    // Update the Employee details in the database
    $updateQuery = "UPDATE emp SET name = ?, salary = ? WHERE id = ?";
    
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("sdi", $empName, $salary, $empId);
        if ($stmt->execute()) {
            echo "Employee updated successfully.";
            // Redirect back to the Employee list or display success message
            // Commented out for debugging purposes
            header("Location: ../emp.php"); 
            exit();
        } else {
            // Handle error if needed
            echo "Error updating Employee: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Handle error if needed
        echo "Error preparing statement: " . $conn->error();
    }
    
    $conn->close();
}
?>
