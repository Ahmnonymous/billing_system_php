<select class="form-control product" name="product[]" required>
    <?php
    
    include 'db_connection.php';
    $sql = "SELECT name FROM product";
    $result = $conn->query($sql);
    echo '<option value="">Select Product</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
        }
    }
    $conn->close();

    ?>
</select>
