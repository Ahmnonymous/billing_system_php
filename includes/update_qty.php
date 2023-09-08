<?php

include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $productId = $_POST["product_id"];
    $date = $_POST["date"];
    $qty = $_POST["qty"];
    
    // Database connection
    include 'db_connection.php';
    
    // Update the product details in the database
    $updateQuery = "UPDATE product SET qty=? WHERE id = ?";
    
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("di" , $qty, $productId);
        $stmt->execute();
        $stmt->close();
        
        // Redirect back to the product list or display success message
        header("Location: ../m_prod.php"); // You need to create this page to show the list of products
        exit();
    } else {
        // Handle error if needed
        echo "Error updating product: " . $conn->error();
    }
    
    $conn->close();
}
?>
