<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form inputs
    $productName = $_POST["name"];
    $quantity = floatval($_POST["qty"]); // Ensure quantity is a float
    $date = $_POST["date"]; // You might want to validate the date format
    
    // Get the product ID based on the product name
    $getProductIdQuery = "SELECT id FROM product WHERE name = ?";
    
    if ($stmt = $conn->prepare($getProductIdQuery)) {
        $stmt->bind_param("s", $productName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $productId = $row["id"];

            
            // Insert the new quantity record into the prod_qty table
            $insertQuantityQuery = "INSERT INTO prod_qty (prod_id, date, quantity) VALUES (?, ?, ?)";
            
            if ($stmt = $conn->prepare($insertQuantityQuery)) {
                $stmt->bind_param("iss", $productId, $date, $quantity);
                if ($stmt->execute()) {
                    $stmt->close();
                    
                    // Redirect back to the product management page or display success message
                    header("Location: ../m_prod.php"); // You need to create this page to show the list of products
                    exit();
                } else {
                    // Handle error if needed
                    echo "Error inserting quantity record: " . $stmt->error;
                }
            } else {
                // Handle error if needed
                echo "Error inserting quantity record: " . $conn->error();
            }
        } else {
            // Handle error if the product name is not found
            echo "Product not found." . $productName;
        }
    } else {
        // Handle error if needed
        echo "Error fetching product ID: " . $conn->error();
    }
    
    $conn->close();
}
?>
