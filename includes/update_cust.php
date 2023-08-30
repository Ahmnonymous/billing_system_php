<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    echo "Script is being executed!<br>";
    $custId = $_POST["id"];
    $custName = $_POST["name"];
    $phone = $_POST["phone"];
    
    include 'db_connection.php';
    
    echo "Customer ID: $custId<br>";
    echo "Customer Name: $custName<br>";
    echo "Phone: $phone<br>";
    
    // Update the customer details in the database
    $updateQuery = "UPDATE cust SET name = ?, phone = ? WHERE id = ?";
    
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("sdi", $custName, $phone, $custId);
        if ($stmt->execute()) {
            echo "Customer updated successfully.";
            // Redirect back to the customer list or display success message
            // Commented out for debugging purposes
            header("Location: ../cust.php"); 
            exit();
        } else {
            // Handle error if needed
            echo "Error updating customer: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Handle error if needed
        echo "Error preparing statement: " . $conn->error();
    }
    
    $conn->close();
}
?>
