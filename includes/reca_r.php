<?php
include 'db_connection.php';

$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

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
    <title>Income Report for <?= $selectedDate ?></title> <!-- Adjusted title -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2>Income Report for <?= $selectedDate ?></h2> <!-- Adjusted heading -->
        <hr>
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

</body>
</html>
