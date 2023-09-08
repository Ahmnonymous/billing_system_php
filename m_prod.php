<?php include 'includes/header.php'; ?>

<?php
$product = null;

// Fetch product details based on the provided ID for editing
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    include 'includes/db_connection.php';
    
    $sql = "SELECT product.id, prod_qty.date, product.name, prod_qty.quantity FROM product, prod_qty WHERE product.id = prod_qty.prod_id AND product.id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    } else {
        // Handle error if needed
        echo "Error fetching product details: " . $conn->error();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($product) ? 'Edit Product Qty' : 'Add Product Qty'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <br><div class="container mt-4 text-center">
    <h2><?php echo isset($product) ? 'Edit Product Qty' : 'Add Product Qty'; ?></h2>
    <hr class="my-4">
    <form action="<?php echo isset($product) ? 'includes/update_qty.php' : 'includes/create_qty.php'; ?>" method="post">
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo isset($product) ? $product['date'] : date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="name">Product Name</label>
                <select class="form-control" id="name" name="name">
                    <option value="" disabled>Select Product</option>
                    <?php
                    // Fetch existing product names and populate the dropdown
                    include 'includes/db_connection.php';
                    $sql = "SELECT id, name FROM product";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $selected = ($product && $product['id'] == $row['id']) ? 'selected' : '';
                        echo '<option value="' . $row['name'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                    }
                    $conn->close();
                    ?>
                </select>
            </div>
            <!-- Quantity field -->
            <div class="col-md-4 mb-3">
                <label for="qty">Quantity</label>
                <input type="number" step="0.01" class="form-control" id="qty" name="qty" value="<?php echo isset($product) ? $product['quantity'] : ''; ?>" required>
            </div>
        </div>
        
        <?php if (isset($product)): ?>
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary"><?php echo isset($product) ? 'Update' : 'Create'; ?></button>
        </div>
    </form>
</div><br>

<!-- Product List View -->
<div class="container mt-4 text-center">
    <h2>Product List</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'includes/db_connection.php';
            
            $sql = "SELECT product.id, product.name, prod_qty.quantity FROM product, prod_qty WHERE product.id = prod_qty.prod_id";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['quantity'] . '</td>';
                    echo "<td><a href=\"includes/del_qty.php?prod_id={$row['id']}&quantity={$row['quantity']}\">Delete</a></td>";
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">No products found</td></tr>';
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

<?php include 'includes/footer.php'; ?>
