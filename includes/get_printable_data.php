<?php
include 'db_connection.php'; // Your database connection

// Get recmId from the AJAX request
$recmId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch details related to the rect table
$sqlRectDetails = "SELECT rect.recm_id, product.name, rect.height, rect.width, rect.sq_ft, rect.qty,rect.total
                   FROM rect
                   INNER JOIN product ON rect.prod_id = product.id
                   WHERE rect.recm_id = ?";
$stmtRectDetails = $conn->prepare($sqlRectDetails);
$stmtRectDetails->bind_param("i", $recmId);
$stmtRectDetails->execute();
$resultRectDetails = $stmtRectDetails->get_result();

// Prepare an array to store fetched data
$data = array(
    'rect_details' => array()
);

while ($rowRectDetails = $resultRectDetails->fetch_assoc()) {
    $data['rect_details'][] = $rowRectDetails;
}

// Fetch data based on recmId from recm table
$sqlRecm = "SELECT recm.date, recm.proj_name AS project_name, recm.id, recm.cust_name AS customer_name, recm.phone, recm.balance, recm.advance, recm.grand_total
            FROM recm
            WHERE recm.id = ?";

$stmtRecm = $conn->prepare($sqlRecm);
$stmtRecm->bind_param("i", $recmId);
$stmtRecm->execute();
$resultRecm = $stmtRecm->get_result();

// Fetch the single row of data from recm table
$recmData = $resultRecm->fetch_assoc();

// Append the recm details to the existing JSON object
$data['recm'] = $recmData;

// Close the database connections
$stmtRecm->close();
$stmtRectDetails->close();
$conn->close();

// Return data as a JSON response
echo json_encode($data);
?>
