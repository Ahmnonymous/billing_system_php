<select class="form-control" id="project" name="project" required>
    <?php
    
    include 'db_connection.php';
    $sql = "SELECT name FROM project";
    $result = $conn->query($sql);
    echo '<option value="">Select Project</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
        }
    }
    $conn->close();

    ?>
</select>
