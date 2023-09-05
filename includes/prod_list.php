
    <?php
    include 'db_connection.php';

    // Check if prodId is provided in the URL
    if (isset($_GET['prodId'])) {
        $prodId = $_GET['prodId'];
        
        // Fetch the product name for the specified prod_id
        $sql = "SELECT name FROM product WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $prodId);
        $stmt->execute();
        $stmt->bind_result($productName);

        while ($stmt->fetch()) {
            echo '<option value="' . $productName . '">' . $productName . '</option>';
        }
    } else {
        // If prodId is not provided, fetch all product names
        echo '<select class="form-control product" name="product[]" required>';
        $sql = "SELECT name FROM product";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
            }
        }
        echo '</select>';
    }
    
    $conn->close();
    ?>

