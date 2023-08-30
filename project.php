<?php
include 'includes/header.php';

$projectId = null;
$projectName = '';

if (isset($_GET['id'])) {
    $projectId = $_GET['id'];
    include 'includes/db_connection.php';
    
    $sql = "SELECT * FROM project WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $project = $result->fetch_assoc();
        if ($project) {
            $projectName = $project['name'];
        }
        $stmt->close();
    } else {
        // Handle error if needed
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $projectId ? 'Edit Project' : 'Add Project'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2><?php echo $projectId ? 'Edit Project' : 'Add Project'; ?></h2>
        <hr class="my-4">
        <form action="<?php echo isset($project) ? 'includes/update_project.php' : 'includes/create_project.php'; ?>" method="post">
        <?php if ($projectId): ?>
            <input type="hidden" name="id" value="<?php echo $projectId; ?>"> <!-- Use "id" as the name -->
        <?php endif; ?>
        <div class="form-group">
            <label for="name">Project Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $projectName; ?>" required>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary"><?php echo $projectId ? 'Update' : 'Create'; ?></button>
        </div>
    </form>

    </div><br><br>

    <div class="container mt-4 text-center">
    <h2>Project List</h2>
    <table class="table">
    <thead>
        <tr>
            <th>Project Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include 'includes/db_connection.php';
        $sql = "SELECT * FROM project";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td><a href="project.php?id=' . $row['id'] . '">Edit</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">No projects found</td></tr>';
        }
        $conn->close();
        ?>
    </tbody>
</table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
