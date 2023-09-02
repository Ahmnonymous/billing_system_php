<?php
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateReceipt'])) {
    // Retrieve the invoice ID, advance, and balance from the POST request
    $invoiceId = $_POST['id'];
    $advance = $_POST['adv'];
    $balance = $_POST['bal'];

    // Prepare and execute a SQL query to update the recm table
    $query = "UPDATE recm SET advance = ?, balance = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ddi', $advance, $balance, $invoiceId); // Assuming 'd' is the data type for double and 'i' is the data type for integer
    $result = $stmt->execute();

    if ($result) {
        // Update successful
        echo json_encode(['success' => 'Receipt updated successfully']);
        header("Location: ../bal.php"); 
    } else {
        // Update failed
        echo json_encode(['error' => 'Failed to update receipt']);
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // Handle invalid request method
    echo json_encode(['error' => 'Invalid request method']);
}
}
?>
