<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get project ID and updated project name from the form
        $projectId = $_POST['id']; // Use "id" to match the input name
        $projectName = $_POST['name'];

    // Validate input
    if (empty($projectId) || empty($projectName)) {
        // Handle validation error if needed
        echo "Project ID and name are required.";
        exit;
    }

    include 'db_connection.php';

    // Update project in the database
    $sql = "UPDATE project SET name = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $projectName, $projectId);
        if ($stmt->execute()) {
            // Success: Redirect to project list or show success message
            header("Location: ../project.php");
            exit;
        } else {
            // Handle update error if needed
            echo "Error updating project.";
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
