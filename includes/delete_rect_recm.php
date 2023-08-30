<?php
include 'db_connection.php'; // Your database connection

if(isset($_POST['id'])) {
    $recmId = $_POST['id'];

    // Begin a transaction for atomicity
    $conn->begin_transaction();

    // Delete corresponding rect records
    $deleteRectQuery = "DELETE FROM rect WHERE recm_id = $recmId";
    if ($conn->query($deleteRectQuery) === TRUE) {
        // Delete corresponding recm record
        $deleteRecmQuery = "DELETE FROM recm WHERE id = $recmId";
        if ($conn->query($deleteRecmQuery) === TRUE) {
            // Commit the transaction
            $conn->commit();
            echo "Record deleted successfully.";
        } else {
            // Rollback the transaction if recm deletion fails
            $conn->rollback();
            echo "Error deleting record: " . $conn->error();
        }
    } else {
        // Rollback the transaction if rect deletion fails
        $conn->rollback();
        echo "Error deleting record: " . $conn->error();
    }
} else {
    echo "Invalid request.";
}

// Close the connection
$conn->close();
?>
