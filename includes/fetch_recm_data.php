<?php
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the invoice ID from the POST request
    $invoiceId = $_POST['invoiceId'];
    error_log("Received invoiceId: " . $invoiceId);

    // Prepare and execute a SQL query to fetch data based on the invoice ID
    $query = "SELECT id,cust_name, proj_name, date, grand_total,advance,balance FROM recm WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $invoiceId); // Assuming 'i' is the data type for invoice ID (integer)
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row was found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Create an associative array with the fetched data
        $data = [
            'id' => $row['id'],
            'cust_name' => $row['cust_name'],
            'proj_name' => $row['proj_name'],
            'date' => $row['date'],
            'grand_total' => $row['grand_total'],
            'advance' => $row['advance'],
            'balance' => $row['balance']
        ];

        // Set the JSON content type header
        header('Content-Type: application/json');

        // Return the data as JSON
        echo json_encode($data);
    } else {
        // No data found for the given invoice ID
        echo json_encode(['error' => 'Invoice not found']);
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // Handle invalid request method
    echo json_encode(['error' => 'Invalid request method']);
}
?>
