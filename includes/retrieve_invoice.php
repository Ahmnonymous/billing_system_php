<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invoiceNumber'])) {
    $invoiceNumber = $_POST['invoiceNumber'];
    
    // Perform a database query to retrieve data based on the invoice number
    $query = "SELECT * FROM recm WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $invoiceNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Retrieve data from 'rect' table based on the invoice number
        $query = "SELECT * FROM rect WHERE recm_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $data['id']);
        $stmt->execute();
        $rectResult = $stmt->get_result();
        
        $rectData = [];
        while ($row = $rectResult->fetch_assoc()) {
            $rectData[] = $row;
        }
        
        $data['rect'] = $rectData;
        
        // Return the data as JSON
        echo json_encode($data);
    } else {
        // Return null if the invoice number is not found
        echo json_encode(null);
    }
} else {
    echo "Invalid request";
}
?>
