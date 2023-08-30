<select class="form-control" id="cust_name" name="cust_name" required>
    <?php
    
    include 'db_connection.php';
    $sql = "SELECT name FROM cust";
    $result = $conn->query($sql);
    echo '<option value="">Select Customer</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
        }
    }
    $conn->close();

    ?>
</select>
