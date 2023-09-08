<?php
include 'db_connection.php'; // Your database connection

if(isset($_GET['id'])) {
    $recmId = intval($_GET['id']); // Make sure the ID is an integer

    // Begin a transaction for atomicity
    $conn->begin_transaction();

    // Use a prepared statement to delete the record
    $deleteRectQuery = "DELETE FROM product WHERE id = ?";
    $stmt = $conn->prepare($deleteRectQuery);
    $stmt->bind_param("i", $recmId);

    if ($stmt->execute() === TRUE) {
        // Commit the transaction if the deletion is successful
        $conn->commit();
        header("Location: ../prod.php"); // Redirect to prod.php
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
