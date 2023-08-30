<?php

include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $productId = $_POST["product_id"];
    $productName = $_POST["name"];
    $category = $_POST["category"];
    $newCategory = $_POST["new_category"];
    $rate = $_POST["rate"];
    
    // Database connection
    include 'db_connection.php';
    
    // Update the product details in the database
    $updateQuery = "UPDATE product SET name = ?, category = ?, rate = ? WHERE id = ?";
    
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("ssdi", $productName, $category, $rate, $productId);
        $stmt->execute();
        $stmt->close();
        
        // If a new category is provided, insert it into the database
        if (!empty($newCategory)) {
            $insertNewCategoryQuery = "INSERT INTO product (name, category, rate) VALUES (?, ?, ?)";
            if ($insertStmt = $conn->prepare($insertNewCategoryQuery)) {
                $insertStmt->bind_param("ssd", $productName, $newCategory, $rate);
                $insertStmt->execute();
                $insertStmt->close();
            }
        }
        
        // Redirect back to the product list or display success message
        header("Location: ../prod.php"); // You need to create this page to show the list of products
        exit();
    } else {
        // Handle error if needed
        echo "Error updating product: " . $conn->error();
    }
    
    $conn->close();
}
?>
