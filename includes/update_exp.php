<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    echo "Script is being executed!<br>";
    $expId = $_POST["id"];
    $expdate = $_POST["selected_date"];
    $expName = $_POST["name"];
    $amount = $_POST["amount"];
    
    
    // Update the expense details in the database
    $updateQuery = "UPDATE exp SET date = ?, name = ?, amount = ? WHERE id = ?";
    
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("ssdi", $expdate,$expName, $amount, $expId);
        if ($stmt->execute()) {
            echo "expense updated successfully.";
            // Redirect back to the expense list or display success message
            // Commented out for debugging purposes
            header("Location: ../exp.php"); 
            exit();
        } else {
            // Handle error if needed
            echo "Error updating expense: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Handle error if needed
        echo "Error preparing statement: " . $conn->error();
    }
    
    $conn->close();
}
?>
