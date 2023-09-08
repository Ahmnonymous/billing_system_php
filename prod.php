<?php include 'includes/header.php'; ?>

<?php
$product = null;

// Fetch product details based on the provided ID for editing
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    include 'includes/db_connection.php';
    
    $sql = "SELECT * FROM product WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
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
    <title><?php echo isset($product) ? 'Edit Product' : 'Add Product'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <br><div class="container mt-4 text-center">
    <h2><?php echo isset($product) ? 'Edit Product' : 'Add Product'; ?></h2>
    <hr class="my-4">
    <form action="<?php echo isset($product) ? 'includes/update_product.php' : 'includes/create_product.php'; ?>" method="post">
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($product) ? $product['name'] : ''; ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="category">Category</label>
                <select class="form-control" id="category" name="category">
                    <option value="" disabled>Select Category</option>
                    <?php
                    // Fetch existing categories and populate the dropdown
                    include 'includes/db_connection.php';
                    $sql = "SELECT DISTINCT category FROM product";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $selected = ($product && $product['category'] == $row['category']) ? 'selected' : '';
                        echo '<option value="' . $row['category'] . '" ' . $selected . '>' . $row['category'] . '</option>';
                    }
                    $conn->close();
                    ?>
                </select>
                <input type="text" class="form-control mt-2" id="new_category" name="new_category" value="" placeholder="New Category">
            </div>
            <div class="col-md-3 mb-3">
                <label for="rate">Rate</label>
                <input type="number" step="0.01" class="form-control" id="rate" name="rate" value="<?php echo isset($product) ? $product['rate'] : ''; ?>" required>
            </div>
            <!-- Quantity field is in the same div as Rate field -->
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
                <th>Category</th>
                <th>Rate</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'includes/db_connection.php';
            
            $sql = "SELECT * FROM product";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['category'] . '</td>';
                    echo '<td>' . $row['rate'] . '</td>';
                    echo '<td><a href="prod.php?id=' . $row['id'] . '">Edit</a></td>';
                    echo '<td><a href="includes/del_prod.php?id='. $row['id'] . '">Delete</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5">No products found</td></tr>';
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
