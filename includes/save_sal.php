<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empName = $_POST["emp_name"];
    $salaryDate = $_POST["date"];
    $salaryAmount = $_POST["amount"];

    // Retrieve employee ID based on employee name
    $empIdQuery = "SELECT id FROM emp WHERE name = ?";
    if ($stmt = $conn->prepare($empIdQuery)) {
        $stmt->bind_param("s", $empName);
        $stmt->execute();
        $stmt->bind_result($empId);
        $stmt->fetch();
        $stmt->close();

        // Insert salary data into the sal table
        $insertQuery = "INSERT INTO sal (emp_id, date, amount) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($insertQuery)) {
            $stmt->bind_param("iss", $empId, $salaryDate, $salaryAmount);
            $stmt->execute();
            $stmt->close();

            header("Location: ../sal.php");
            exit();
        } else {
            echo "Error creating salary entry: " . $conn->error;
        }
    } else {
        echo "Error retrieving employee ID: " . $conn->error;
    }

    $conn->close();
}
?>
