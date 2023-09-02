<?php
include 'db_connection.php'; 

function getEntityId($conn, $tableName, $fieldName, $value) {
    $query = "SELECT id FROM $tableName WHERE $fieldName = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row["id"] : null;
}

function insertRecord($conn, $query, $bindTypes, $values) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($bindTypes, ...$values);
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form inputs
    $custName = $_POST["cust_name"];
    $project = $_POST["proj_name"];
    $phone = $_POST["phone"];
    $date = $_POST["date"];
    $advance = $_POST["adv"];
    $balance = $_POST["bal"];
    $grandTotal = $_POST["g_total"];

    // Insert data into the 'recm' table
    $recmQuery = "INSERT INTO recm (cust_name, proj_name, date, phone, advance, balance, grand_total) 
    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $recmValues = [$custName, $project, $date, $phone, $advance, $balance, $grandTotal];
    $recmId = insertRecord($conn, $recmQuery, "ssssddd", $recmValues);

    
    if ($recmId !== false) {
        // Insert data into the 'rect' table
        $rectQuery = "INSERT INTO rect (recm_id, prod_id, ser_no, height, width, sq_ft,qty, total) 
                      VALUES (?, ?, ?, ?, ?, ?,?,?)";
        $rectStmt = $conn->prepare($rectQuery);
        foreach ($_POST["ser_no"] as $key => $serNo) {
            $height = $_POST["height"][$key];
            $width = $_POST["width"][$key];
            $sqft = $_POST["sqft"][$key];
            $qty = $_POST["qty"][$key];
            $productName = $_POST["product"][$key];
            $total = $_POST["total"][$key];
            
            $productId = getEntityId($conn, "product", "name", $productName);
            
            $rectValues = [$recmId, $productId, $serNo, $height, $width, $sqft,$qty, $total];
            $rectStmt->bind_param("iissddid", ...$rectValues);
            $rectStmt->execute();
        }
        $rectStmt->close();

        // Redirect to success page
        header("Location: ../rec.php");
        exit();
    } else {
        // Handle errors
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
