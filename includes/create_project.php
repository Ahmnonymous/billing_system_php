<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get project name from the form
    $projectName = $_POST['name'];

    // Validate input
    if (empty($projectName)) {
        // Handle validation error if needed
        echo "Project name is required.";
        exit;
    }

    include 'db_connection.php';

    // Insert project into the database
    $sql = "INSERT INTO project (name) VALUES (?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $projectName);
        if ($stmt->execute()) {
            // Success: Redirect to project list or show success message
            header("Location: ../project.php");
            exit;
        } else {
            // Handle insert error if needed
            echo "Error creating project.";
        }
        $stmt->close();
    } else {
        // Handle database error if needed
        echo "Database error.";
    }

    $conn->close();
} else {
    // Redirect if accessed directly
    header("Location: ../project.php");
    exit;
}
?>
