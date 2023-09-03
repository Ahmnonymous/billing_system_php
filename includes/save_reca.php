<?php
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $expdate = $_POST["selected_date"];
    $expName = $_POST["name"];
    $amount = $_POST["amount"];
    
    // Insert the new product into the database
    $insertQuery = "INSERT INTO reca (date,name, amount) VALUES (?,?, ?)";
    
    if ($stmt = $conn->prepare($insertQuery)) {
        $stmt->bind_param("ssd", $expdate,$expName, $amount);
        $stmt->execute();
        $stmt->close();
        
        // Redirect back to the product list or display success message
        header("Location: ../reca.php"); // You need to create this page to show the list of products
        exit();
    } else {
        // Handle error if needed
        echo "Error creating product: " . $conn->error();
    }
    
    $conn->close();
}
?>
