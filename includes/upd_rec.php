<?php
include 'db_connection.php';

function updateRecord($conn, $query, $bindTypes, $values) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($bindTypes, ...$values);
    if ($stmt->execute()) {
        return true; // Successfully updated
    }
    return false; // Failed to update
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $invoiceId = $_POST["invoice"]; // Assuming you have an invoice ID passed in the form

    // Update data in the 'recm' table
    $recmQuery = "UPDATE recm SET cust_name=?, proj_name=?, date=?, phone=?, disc=?, advance=?, balance=?, grand_total=? WHERE id=?";
    $recmValues = [$_POST["cust_name"], $_POST["proj_name"], $_POST["date"], $_POST["phone"], $_POST["dis"], $_POST["adv"], $_POST["bal"], $_POST["g_total"], $invoiceId];

    if ($invoiceId !== false) {
        if (updateRecord($conn, $recmQuery, "ssssddddi", $recmValues)) {
            // Update data in the 'rect' table
            $rectUpdateQuery = "UPDATE rect SET prod_id=?, height=?, width=?, sq_ft=?, qty=? WHERE recm_id=? AND total=?";
            $rectStmt = $conn->prepare($rectUpdateQuery);

            foreach ($_POST["total"] as $key => $total) {
                $height = $_POST["height"][$key];
                $width = $_POST["width"][$key];
                $sqft = $_POST["sqft"][$key];
                $qty = $_POST["qty"][$key];
                $productName = $_POST["product"][$key];

                $productId = getEntityId($conn, "product", "name", $productName);

                $rectValues = [$productId, $height, $width, $sqft, $qty, $invoiceId, $total];
                $rectStmt->bind_param("issddii", ...$rectValues);
                $rectStmt->execute();
            }
            $rectStmt->close();

            // Redirect to success page or display a success message
            header("Location: ../u_rec.php");
            exit();
        } else {
            // Handle errors
            echo "Error: " . $conn->error();
        }
    } else {
        // Handle errors
        echo "Error: Invoice ID not provided";
    }

    // Close the database connection
    $conn->close();
}
?>
