<?php
include 'db_connection.php';

if (isset($_GET['product'])) {
    $productName = $_GET['product'];
    
    $sql = "SELECT rate FROM product WHERE name = '$productName'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['rate'];
    }
}

$conn->close();
?>
