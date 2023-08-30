<?php
include 'includes/header.php';
include 'includes/db_connection.php';

$customer = null;

// Fetch customer details based on the provided ID for editing
if (isset($_GET['id'])) {
    $customerId = $_GET['id'];
    
    $sql = "SELECT * FROM cust WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
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
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2><?php echo isset($customer) ? 'Edit Customer' : 'Add Customer'; ?></h2>
        <hr class="my-4">
        
        <!-- Customer Input Form -->
        <form action="<?php echo isset($customer) ? 'includes/update_cust.php' : 'includes/save_cust.php'; ?>" method="post">
        <?php if (isset($customer)) { ?>
        <!-- Include a hidden input field for customer ID -->
        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
        <?php } ?>
            <div class="form-row">
                <div class="col-md-6 mb-2">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($customer) ? $customer['name'] : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="phone">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($customer) ? $customer['phone'] : ''; ?>" required>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary mt-4">Save Customer</button>
            </div>
        </form><br>
        
        <!-- Customer Table View -->
        <div class="container mt-4 text-center">
            <h3>Customer List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'includes/db_connection.php'; // Your database connection details here
                    
                    $sql = "SELECT id, name, phone FROM cust";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['phone'] . '</td>';
                            echo '<td><a href="cust.php?id=' . $row['id'] . '">Edit</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">No customers found</td></tr>';
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
