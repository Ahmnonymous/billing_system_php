<?php include 'header.php'; ?>

<?php
// Fetch invoice information from the database
include 'db_connection.php';

$sql = "SELECT recm.id, recm.invoice, cust.name AS cust_name, project.name AS project_name
        FROM recm
        JOIN cust ON recm.cust_id = cust.id
        JOIN project ON recm.project_id = project.id";

$result = $conn->query($sql);
?>

<div class="container mt-4 text-center">
    <div class="jumbotron" style="background-color: #f8f9fa;">
        <h2>View and Manage Invoices</h2>
        <hr class="my-4">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Customer Name</th>
                    <th>Project Name</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['invoice'] . '</td>';
                        echo '<td>' . $row['cust_name'] . '</td>';
                        echo '<td>' . $row['project_name'] . '</td>';
                        echo '<td><a href="rec.php?id=' . $row['id'] . '">Edit</a></td>';
                        echo '<td><a href="delete_invoice.php?id=' . $row['id'] . '">Delete</a></td>';
                        echo '</tr>';
                    }
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
