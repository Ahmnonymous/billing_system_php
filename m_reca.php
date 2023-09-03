<?php
include 'includes/header.php';
include 'includes/db_connection.php';

$selectedDate = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d');

// Retrieve income data for the selected date
$incomeData = getIncomeData($conn, $selectedDate);

// Function to retrieve income data
function getIncomeData($conn, $selectedDate)
{
    $incomeData = [];

    // Query to retrieve income data for the selected date
    $incomeQuery = "SELECT name, amount, date FROM reca WHERE date = '$selectedDate'";
    $incomeResult = $conn->query($incomeQuery);

    if ($incomeResult->num_rows > 0) {
        while ($row = $incomeResult->fetch_assoc()) {
            $incomeData[] = $row;
        }
    }

    return $incomeData;
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
        <h2>Receiving Amount Report</h2>
        <hr>
    </div>

    <div class="container mt-4 text-center">
        <br>
        <form method="POST" class="d-inline-block">
            <div class="form-group">
                <label for="selected_date" class="mr-2">Select Date</label>
                <input type="date" id="selected_date" name="selected_date" class="form-control" required value="<?php echo $selectedDate; ?>">
            </div>
            <button type="submit" class="btn btn-primary ml-2">Submit</button>
            <button class="btn btn-primary" id="printButton">Print Report</button>
        </form>
    </div>

    <div class="container mt-4 text-center">
        <div class="d-flex">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Detail</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($incomeData as $incomeRow): ?>
                    <tr>
                        <td><?= $incomeRow['name'] ?></td>
                        <td><?= $incomeRow['amount'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
            var reportUrl = "includes/reca_r.php?date=" + date;  // Correct the URL parameter
            window.open(reportUrl, '_blank');
        }
    });
    </script>
</body>
</html>
