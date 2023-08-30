<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    echo "Script is being executed!<br>";
    $expId = $_POST["id"];
    $expName = $_POST["name"];
    $amount = $_POST["amount"];
    
    include 'db_connection.php';
    
    echo "expense ID: $expId<br>";
    echo "expense Name: $expName<br>";
    echo "amount: $amount<br>";
    
    // Update the expense details in the database
    $updateQuery = "UPDATE exp SET name = ?, amount = ? WHERE id = ?";
    
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("sdi", $expName, $amount, $expId);
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
