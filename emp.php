<?php
include 'includes/header.php';
include 'includes/db_connection.php';

$employee = null;

// Fetch employee details based on the provided ID for editing
if (isset($_GET['id'])) {
    $employeeId = $_GET['id'];
    
    $sql = "SELECT * FROM emp WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        $stmt->close();
    } else {
        // Handle error if needed
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2><?php echo isset($employee) ? 'Edit Employee' : 'Add Employee'; ?></h2>
        <hr class="my-4">
        
        <!-- employee Input Form -->
        <form action="<?php echo isset($employee) ? 'includes/update_emp.php' : 'includes/save_emp.php'; ?>" method="post">
        <?php if (isset($employee)) { ?>
        <!-- Include a hidden input field for employee ID -->
        <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
        <?php } ?>
            <div class="form-row">
                <div class="col-md-6 mb-2">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($employee) ? $employee['name'] : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="salary">salary</label>
                    <input type="tel" class="form-control" id="salary" name="salary" value="<?php echo isset($employee) ? $employee['salary'] : ''; ?>" required>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary mt-4">Save employee</button>
            </div>
        </form><br>
        
        <!-- employee Table View -->
        <div class="container mt-4 text-center">
            <h3>Employee List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Salary</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'includes/db_connection.php'; // Your database connection details here
                    
                    $sql = "SELECT id, name, salary FROM emp";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['salary'] . '</td>';
                            echo '<td><a href="emp.php?id=' . $row['id'] . '">Edit</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">No employees found</td></tr>';
                    }
                    
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
