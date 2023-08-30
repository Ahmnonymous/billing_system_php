<?php
include 'db_connection.php';

if (isset($_GET['cust_name'])) {
    $customerName = $_GET['cust_name'];
    
    $sql = "SELECT phone FROM cust WHERE name = '$customerName'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['phone'];
    }
}

$conn->close();
?>
