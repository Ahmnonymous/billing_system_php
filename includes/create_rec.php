<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = '786$toqA';
$dbname = "bill";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getEntityId($conn, $tableName, $fieldName, $value) {
    $query = "SELECT id FROM $tableName WHERE $fieldName = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row["id"] : null;
    }
    return null;
}

function insertRecord($conn, $query, $bindTypes, $values) {
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param($bindTypes, ...$values);
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $custName = $_POST["cust_name"];
    $project = $_POST["project"];
    $phone = $_POST["phone"];
    $date = $_POST["date"];
    $invoice = $_POST["invoice"];
    $advance = $_POST["adv"];
    $balance = $_POST["bal"];
    $grandTotal = $_POST["g_total"];

    // Get customer and project IDs
    $custId = getEntityId($conn, "cust", "name", $custName);
    $projectId = getEntityId($conn, "project", "name", $project);

    // Insert data into the 'recm' table
    $recmQuery = "INSERT INTO recm (cust_id, project_id, invoice, date, phone, advance, balance, grand_total) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $recmValues = [$custId, $projectId, $invoice, $date, $phone, $advance, $balance, $grandTotal];
    
    $recmId = insertRecord($conn, $recmQuery, "iisssddd", $recmValues);
    
    if ($recmId !== false) {
        // Insert data into the 'rect' table
        $rectQuery = "INSERT INTO rect (recm_id, prod_id, ser_no, height, width, sq_ft, total) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($rectStmt = $conn->prepare($rectQuery)) {
            foreach ($_POST["ser_no"] as $key => $serNo) {
                $height = $_POST["height"][$key];
                $width = $_POST["width"][$key];
                $sqft = $_POST["sqft"][$key];
                $productName = $_POST["product"][$key];
                $total = $_POST["total"][$key];
                
                $productId = getEntityId($conn, "product", "name", $productName);
                
                $rectValues = [$recmId, $productId, $serNo, $height, $width, $sqft, $total];
                $rectStmt->bind_param("iissddd", ...$rectValues);
                $rectStmt->execute();
            }
            $rectStmt->close();
        }

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
