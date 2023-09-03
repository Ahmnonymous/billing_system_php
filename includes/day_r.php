<?php
include 'db_connection.php';

// Initialize variables
$tot_rec = 0;
$tot_exp = 0;
$tot_sq = 0;
$incomeData = [];
$expenditureData = [];
$projectData = [];
$selectedDate = null;

// Function to retrieve income data
function getIncomeData($conn, $selectedDate)
{
    $incomeData = [];
    $tot_rec = 0;
    $tot_sq = 0;

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

    return [$incomeData, $tot_rec, $tot_sq];
}

// Function to retrieve expenditure data
function getExpenditureData($conn, $selectedDate)
{
    $expenditureData = [];
    $tot_exp = 0;

    // Retrieve expenditure data based on the selected date
    $expenditureQuery = "SELECT id, name, amount FROM exp WHERE date = '$selectedDate'";
    $expenditureResult = $conn->query($expenditureQuery);

    if ($expenditureResult->num_rows > 0) {
        // Loop through expenditure data
        while ($row = $expenditureResult->fetch_assoc()) {
            $expenditureData[] = $row;
            $tot_exp += $row['amount']; // Calculate total expenditure
        }
    }

    return [$expenditureData, $tot_exp];
}

// Function to retrieve project-wise total square feet data
function getProjectData($conn, $selectedDate)
{
    $projectData = [];

    // Retrieve project names and total square feet used
    $projectQuery = "SELECT recm.proj_name, SUM(rect.sq_ft) AS total_sq_ft FROM recm,rect
    where recm.id = rect.recm_id
    and recm.date = '$selectedDate' GROUP BY proj_name";
    $projectResult = $conn->query($projectQuery);

    if ($projectResult->num_rows > 0) {
        // Loop through project data
        while ($row = $projectResult->fetch_assoc()) {
            $projectName = $row['proj_name'];
            $totalSqFt = $row['total_sq_ft'];
            $projectData[$projectName] = $totalSqFt;
        }
    }

    return $projectData;
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['date']) ) {

    $selectedDate = $_GET['date'];

    list($incomeData, $tot_rec, $tot_sq) = getIncomeData($conn, $selectedDate);
    list($expenditureData, $tot_exp) = getExpenditureData($conn, $selectedDate);
    $projectData = getProjectData($conn, $selectedDate);

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


    <div class="container mt-4 text-left">
        <br>
        <table class="table table-bordered vertical-borders">
            <thead>
                <tr>
                    <th colspan="1">Total Balance</th>
                    <th colspan="1"><span><?php echo ($tot_rec-$tot_exp); ?></span></th>
                    <th colspan="1">Expenditures</th>
                    <th colspan="1"><?php echo $tot_exp; ?></th>
                </tr>
                <tr>
                    <th colspan="1">Income</th>
                    <th colspan="1"><?php echo $tot_rec; ?></th>
                    <th colspan="1">Total Feet</th>
                    <th colspan="1"><?php echo $tot_sq; ?></th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="container mt-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Total Sq Ft</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display project-wise total square feet in a table
                foreach ($projectData as $projectName => $totalSqFt) {
                    echo '<tr>';
                    echo '<td>' . $projectName . '</td>';
                    echo '<td>' . $totalSqFt . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>


    <div class="container mt-4 text-center">
    <div class="d-flex justify-content-between">
        <!-- Income Table -->
        <table class="table table-bordered" style="table-layout: auto; width: 75%;">
            <thead>
                <tr>
                    <th colspan="6">Income</th>
                </tr>
                <tr>
                    <th style="width: 10%;">Invoice</th>
                    <th style="width: 25%;">Name</th>
                    <th style="width: 16.25%;">Size (Sq. ft)</th>
                    <th style="width: 16.25%;">Total Amount</th>
                    <th style="width: 16.25%;">Advance</th>
                    <th style="width: 16.25%;">Balance</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($incomeData as $recmId => $incomeRow): ?>
                <tr>
                    <td style="width: 10%;"><?= $recmId ?></td>
                    <td style="width: 25%;"><?= $incomeRow['name'] ?></td>
                    <td style="width: 16.25%;"><?= $incomeRow['sq_ft'] ?></td>
                    <td style="width: 16.25%;"><?= $incomeRow['grand_total'] ?></td>
                    <td style="width: 16.25%;"><?= $incomeRow['advance'] ?></td>
                    <td style="width: 16.25%;"><?= $incomeRow['balance'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Expenditure Table -->
        <table class="table table-bordered" style="table-layout: auto; width: 25%;">
            <thead>
                <tr>
                    <th colspan="2">Expenditures</th>
                </tr>
                <tr>
                    <th style="width: 50%;">Details</th>
                    <th style="width: 50%;">Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($expenditureData as $expenseRow): ?>
                <tr>
                    <td style="width: 50%;"><?= $expenseRow['name'] ?></td>
                    <td style="width: 50%;"><?= $expenseRow['amount'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
