<?php
include 'db_connection.php'; // Your database connection

if(isset($_GET['id'])) {
    $recmId = $_GET['id'];

    // Construct and execute the SQL query to fetch details from the rect table
    $detailsQuery = "SELECT rect.recm_id, product.name,  rect.height, rect.width, rect.sq_ft, rect.total
                    FROM rect, product
                    WHERE rect.prod_id = product.id
                    AND rect.recm_id = $recmId";

    $detailsResult = $conn->query($detailsQuery);

    if ($detailsResult->num_rows > 0) {
        // Display details in a table
        echo '<table class="table">';
        echo '<tr><th>Product Name</th><th>Height</th><th>Width</th><th>Square Feet</th><th>Total</th></tr>';
        
        while ($detailsRow = $detailsResult->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $detailsRow['name'] . '</td>';
            echo '<td>' . $detailsRow['height'] . '</td>';
            echo '<td>' . $detailsRow['width'] . '</td>';
            echo '<td>' . $detailsRow['sq_ft'] . '</td>';
            echo '<td>' . $detailsRow['total'] . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    } else {
        echo "No details found.";
    }
} else {
    echo "Invalid request.";
}
?>
