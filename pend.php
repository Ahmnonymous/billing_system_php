<?php include 'includes/header.php'?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt View</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2>Pending Amounts</h2>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>Ser No.</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database connection
                include 'includes/db_connection.php';

                // Retrieve data from the database using the query
                $sql = "SELECT recm.date, recm.invoice, cust.name, cust.phone, recm.balance
                        FROM recm, cust
                        WHERE recm.cust_id = cust.id";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $serialNumber = 1; // Initialize serial number
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $serialNumber . '</td>';
                        echo '<td>' . $row['date'] . '</td>';
                        echo '<td>' . $row['invoice'] . '</td>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . $row['phone'] . '</td>';
                        echo '<td>' . $row['balance'] . '</td>';
                        echo '</tr>';
                        $serialNumber++; // Increment serial number
                    }
                } else {
                    echo '<tr><td colspan="6">No records found</td></tr>';
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
