<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $custName = $_POST["name"];
    $phone = $_POST["phone"];
    
    // Insert the new product into the database
    $insertQuery = "INSERT INTO cust (name, phone) VALUES (?, ?)";
    
    if ($stmt = $conn->prepare($insertQuery)) {
        $stmt->bind_param("sd", $custName, $phone);
        $stmt->execute();
        $stmt->close();
        
        // Redirect back to the product list or display success message
        header("Location: ../cust.php"); // You need to create this page to show the list of products
        exit();
    } else {
        // Handle error if needed
        echo "Error creating product: " . $conn->error();
    }
    
    $conn->close();
}
?>
