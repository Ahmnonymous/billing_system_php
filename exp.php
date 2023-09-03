<?php
include 'includes/header.php';
include 'includes/db_connection.php';

$expense = null;

// Fetch expense details based on the provided ID for editing
if (isset($_GET['id'])) {
    $expenseId = $_GET['id'];
    
    $sql = "SELECT * FROM exp WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $expenseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $expense = $result->fetch_assoc();
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
    <title>BillSys - Expense Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2><?php echo isset($expense) ? 'Edit Expense' : 'Add Expense'; ?></h2>
        <hr class="my-4">
        
        <!-- expense Input Form -->
        <form action="<?php echo isset($expense) ? 'includes/update_exp.php' : 'includes/save_exp.php'; ?>" method="post">
        <?php if (isset($expense)) { ?>
        <!-- Include a hidden input field for expense ID -->
        <input type="hidden" name="id" value="<?php echo $expense['id']; ?>">
        <?php } ?>
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <label for="selected_date">Select Date</label>
                    <input type="date" class="form-control" id="selected_date" name="selected_date" value="<?php echo isset($expense) ? $expense['date'] : date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($expense) ? $expense['name'] : ''; ?>" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" value="<?php echo isset($expense) ? $expense['amount'] : ''; ?>" required>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary mt-4">Save expense</button>
            </div>
        </form><br>
        
        <!-- expense Table View -->
        <div class="container mt-4 text-center">
            <h3>Expense List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Expense Name</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'includes/db_connection.php'; // Your database connection details here
                    
                    $sql = "SELECT id, name, amount FROM exp";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['amount'] . '</td>';
                            echo '<td><a href="exp.php?id=' . $row['id'] . '">Edit</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">No expenses found</td></tr>';
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
