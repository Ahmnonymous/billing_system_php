<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $empName = $_POST["name"];
    $salary = $_POST["salary"];
    
    // Insert the new product into the database
    $insertQuery = "INSERT INTO emp (name, salary) VALUES (?, ?)";
    
    if ($stmt = $conn->prepare($insertQuery)) {
        $stmt->bind_param("sd", $empName, $salary);
        $stmt->execute();
        $stmt->close();
        
        // Redirect back to the product list or display success message
        header("Location: ../emp.php"); // You need to create this page to show the list of products
        exit();
    } else {
        // Handle error if needed
        echo "Error creating product: " . $conn->error();
    }
    
    $conn->close();
}
?>
