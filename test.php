<?php
include 'includes/header.php';
include 'includes/db_connection.php';

$tot_rec = 0;
$tot_exp = 0;
$tot_sq = 0;
$incomeData = array();
$expenditureData = array();

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDate = $_POST['selected_date'];

    // Retrieve data from the database using the query for income
    $incomeQuery = "SELECT recm.invoice, CONCAT(cust.name, ' (', project.name, ')') AS name, SUM(rect.sq_ft) AS sq_ft, recm.balance, recm.grand_total, recm.advance
    FROM recm
    JOIN cust ON recm.cust_id = cust.id
    JOIN project ON recm.project_id = project.id
    JOIN rect ON recm.id = rect.recm_id
    WHERE recm.recm_date = '$selectedDate'
    GROUP BY recm.invoice";

    $incomeResult = $conn->query($incomeQuery);

    if ($incomeResult->num_rows > 0) {
        // Retrieve expenditure data before looping through income data
        $expenditureQuery = "SELECT name, amount FROM exp WHERE date = '$selectedDate'";
        $expenditureResult = $conn->query($expenditureQuery);
        
        if ($expenditureResult->num_rows > 0) {
            while ($expRow = $expenditureResult->fetch_assoc()) {
                $expenditureData[] = $expRow;
                $tot_exp += $expRow['amount'];
            }
        }

        // Loop through income data
        while ($row = $incomeResult->fetch_assoc()) {
            $incomeData[] = $row;
            $tot_rec += $row['advance'];
            $tot_sq += $row['sq_ft'];
        }
    }
}

$conn->close();
?>

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
        <h2>Day Book - Daily Report</h2>
        <hr>
    </div>

    <div class="container mt-4 text-center">
        <br>
        <form method="POST" class="d-inline-block">
            <div class="form-group">
                <label for="selected_date" class="mr-2">Select Date</label>
                <input type="date" id="selected_date" name="selected_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary ml-2">Submit</button>
        </form>
    </div>


    <div class="container mt-4 text-left">
        <br>
        <table class="table table-bordered vertical-borders">
            <thead>
                <tr>
                    <th colspan="1">Total Balance</th>
                    <th colspan="1"><span><?php echo ($tot_rec-$tot_exp); ?></span></th>
                    <th colspan="3">Total Feet</th>
                    <th colspan="3"><?php echo $tot_sq; ?></th>
                </tr>
                <tr>
                    <th colspan="1">Income</th>
                    <th colspan="1"><?php echo $tot_rec; ?></th>
                    <th colspan="3">Project Name</th>
                    <th colspan="3">Project Name</th>

                </tr>
                <tr>
                    <th colspan="1">Expenditures</th>
                    <th colspan="1"><?php echo $tot_exp; ?></th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="container mt-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="6">Income</th>
                    <th colspan="2">Remaining Amounts</th>
                    <th colspan="2">Expenditures</th>
                </tr>
                <tr>
                    <th>Invoice</th>
                    <th>Customer Name</th>
                    <th>Size (Sq. ft)</th>
                    <th>Total Amount</th>
                    <th>Advance</th>
                    <th>Balance</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Details</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($incomeData as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['invoice'] . '</td>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['sq_ft'] . '</td>';
                    echo '<td>' . $row['grand_total'] . '</td>';
                    echo '<td>' . $row['advance'] . '</td>';
                    echo '<td>' . $row['balance'] . '</td>';
                    
                    // Loop through expenditure data for the current income record
                    foreach ($expenditureData as $expRow) {
                        echo '<td>' . $expRow['name'] . '</td>';
                        echo '<td>' . $expRow['amount'] . '</td>';
                    }
                    
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-4 text-center">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>TOTAL RECEIVED</th>
                <th>EXPENSES</th>
                <th>AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $tot_rec; ?></td>
                <td><?php echo $tot_exp; ?></td>
                <td><?php echo ($tot_rec - $tot_exp); ?></td>
            </tr>
        </tbody>
    </table>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
