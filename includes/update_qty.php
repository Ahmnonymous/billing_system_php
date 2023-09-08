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

            // Update the existing record in prod_qty
            $updateQuantityQuery = "UPDATE prod_qty SET date = ?, quantity = ? WHERE prod_id = ?";
            if ($stmt = $conn->prepare($updateQuantityQuery)) {
                $stmt->bind_param("sdi", $date, $quantity, $productId);
                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: ../m_prod.php"); // Redirect on success
                    exit();
                } else {
                    echo "Error updating quantity record: " . $stmt->error;
                }
            } else {
                echo "Error updating quantity record: " . $conn->error();
            }
        } else {
            // Handle error if the product name is not found
            echo "Product not found: " . $productName;
        }
    } else {
        // Handle error if needed
        echo "Error fetching product ID: " . $conn->error();
    }
    
    $conn->close();
}
?>
