<select class="form-control" id="emp_name" name="emp_name" required>
    <?php
    
    include 'db_connection.php';
    $sql = "SELECT name FROM emp";
    $result = $conn->query($sql);
    echo '<option value="">Select Employee</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
        }
    }
    $conn->close();

    ?>
</select>
