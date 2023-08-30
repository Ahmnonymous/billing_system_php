<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $productName = $_POST["name"];
    $category = $_POST["category"];
    $newCategory = $_POST["new_category"];
    $rate = $_POST["rate"];
    
    // Determine the category value
    if (!empty($newCategory)) {
        $category = $newCategory;
    }
    
    // Insert the new product into the database
    $insertQuery = "INSERT INTO product (name, category, rate) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($insertQuery)) {
        $stmt->bind_param("ssd", $productName, $category, $rate);
        $stmt->execute();
        $stmt->close();
        
        // Redirect back to the product list or display success message
        header("Location: ../prod.php"); // You need to create this page to show the list of products
        exit();
    } else {
        // Handle error if needed
        echo "Error creating product: " . $conn->error;
    }
    
    $conn->close();
}
?>
