<?php
include 'db_connection.php'; // Your database connection

if (isset($_GET['prod_id']) && isset($_GET['quantity'])) {
    $recmId = intval($_GET['prod_id']); // Make sure the ID is an integer
    $quantity = floatval($_GET['quantity']); // Get quantity from the URL

    // Begin a transaction for atomicity
    $conn->begin_transaction();

    // Use a prepared statement to delete the record
    $deleteRectQuery = "DELETE FROM prod_qty WHERE prod_id = ? AND quantity = ?";
    $stmt = $conn->prepare($deleteRectQuery);
    
    // Bind the parameters with their data types
    $stmt->bind_param("id", $recmId, $quantity);

    if ($stmt->execute() === TRUE) {
        // Commit the transaction if the deletion is successful
        $conn->commit();
        header("Location: ../m_prod.php"); // Redirect to prod.php
        exit();
    } else {
        // Rollback the transaction if rect deletion fails
        $conn->rollback();
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

// Close the connection
$conn->close();
?>
