<?php
include 'includes/header.php';
include 'includes/db_connection.php';

$tot_rec = 0;
$tot_exp = 0;
$tot_sq = 0;
$tot_sq_o = 0;
$tot_sq_q = 0;
$incomeData = [];
$expenditureData = [];
$projectData = [];
$selectedDate = null;

// Function to retrieve project-wise total square feet data
function getProjectData($conn, $selectedDate)
{
    $projectData = [];

    // Use a prepared statement to prevent SQL injection
    $projectQuery = "SELECT product.name, SUM(prod_qty.quantity) AS total_qty FROM product, prod_qty
    WHERE prod_qty.prod_id = product.id
    AND prod_qty.date = ?
    GROUP BY product.name";

    $stmt = $conn->prepare($projectQuery);
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $projectName = $row['name'];
            $totalSqFt = $row['total_qty'];
            $projectData[$projectName] = $totalSqFt;
        }
    }

    return $projectData;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDate = $_POST['selected_date'];

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
        <h2>Inventory Report</h2>
        <hr>
    </div>

    <div class="container mt-4 text-center">
        <br>
        <form method="POST" class="d-inline-block">
            <div class="form-group">
                <label for="selected_date" class="mr-2">Select Date</label>
                <input type="date" class="form-control" id="selected_date" name="selected_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary ml-2">Submit</button>
            <button class="btn btn-primary" id="printButton">Print Report</button>
        </form>
    </div>


    <div class="container mt-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
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
            var reportUrl = "includes/qty_r.php?date=" + date;  // Correct the URL parameter
            window.open(reportUrl, '_blank');
        }
    });
</script>

</body>
</html>
