<?php
include 'includes/header.php';
include 'includes/db_connection.php';

$tot_rec = 0;
$tot_exp = 0;
$tot_sq = 0;
$incomeData = array();
$expenditureData = array();
$selectedDate=null;

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDate = $_POST['selected_date'];

    // Retrieve data from the database using the query for income
    $incomeQuery = "SELECT recm.id AS recm_id, CONCAT(recm.cust_name, ' (', recm.proj_name, ')') AS name, SUM(rect.sq_ft) AS sq_ft, recm.balance, recm.grand_total, recm.advance
    FROM recm
    JOIN rect ON recm.id = rect.recm_id
    WHERE recm.recm_date = '$selectedDate'
    GROUP BY recm.id";

    $incomeResult = $conn->query($incomeQuery);

    if ($incomeResult->num_rows > 0) {
        // Loop through income data
        while ($row = $incomeResult->fetch_assoc()) {
            $recmId = $row['recm_id']; // Get the recm_id
            $incomeData[$recmId] = $row;
            $tot_rec += $row['advance'];
            $tot_sq += $row['sq_ft'];
        }
    }

    // Retrieve expenditure data based on the selected date
    $expenditureQuery = "SELECT id, name, amount FROM exp WHERE date = '$selectedDate'";
    $expenditureResult = $conn->query($expenditureQuery);

    if ($expenditureResult->num_rows > 0) {
        // Loop through expenditure data
        while ($row = $expenditureResult->fetch_assoc()) {
            $incomeId = $row['id']; // Get the associated income_id
            // Check if the income_id exists in incomeData
            if (isset($incomeData[$incomeId])) {
                // Add expenditure data to the income record
                $incomeData[$incomeId]['expenditure'][] = $row;
                $tot_exp += $row['amount'];
            }
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
            <button class="btn btn-primary" id="printButton">Print Report</button>
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
                    <th colspan="3">Expenditures</th>
                    <th colspan="3"><?php echo $tot_exp; ?></th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="container mt-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="6">Income</th>
                    <th colspan="2">Expenditures</th>
                </tr>
                <tr>
                    <th>Invoice</th>
                    <th>Customer Name</th>
                    <th>Size (Sq. ft)</th>
                    <th>Total Amount</th>
                    <th>Advance</th>
                    <th>Balance</th>
                    <th>Details</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach ($incomeData as $recmId => $incomeRow) {
                    echo '<tr>';
                    echo '<td>' . $recmId . '</td>';
                    echo '<td>' . $incomeRow['name'] . '</td>';
                    echo '<td>' . $incomeRow['sq_ft'] . '</td>';
                    echo '<td>' . $incomeRow['grand_total'] . '</td>';
                    echo '<td>' . $incomeRow['advance'] . '</td>';
                    echo '<td>' . $incomeRow['balance'] . '</td>';

                    // Check if there is expenditure data for the current income record
                    if (isset($incomeRow['expenditure'])) {
                        foreach ($incomeRow['expenditure'] as $expenditureRow) {
                            echo '<td>' . $expenditureRow['name'] . '</td>';
                            echo '<td>' . $expenditureRow['amount'] . '</td>';
                        }
                    } else {
                        // If no expenditure data found, add empty values
                        echo '<td></td>';
                        echo '<td></td>';
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
    <script>
    $(document).ready(function() {
        // Print Button Click Handler
        $('#printButton').click(function() {
            openPrintTab();
        });

        // Function to Open a New Tab and Print the Report
        function openPrintTab() {
            var date = "<?php echo $selectedDate; ?>";  // Use selectedDate from the form
            var reportUrl = "includes/day_r.php?date=" + date;  // Correct the URL parameter
            window.open(reportUrl, '_blank');
        }
    });
</script>

</body>
</html>
