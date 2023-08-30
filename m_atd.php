<?php include 'includes/header.php';?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance View</title>
    <style>
        .box {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 20px 0;
        }
        
        .box .row {
            display: flex;
            justify-content: space-between;
        }
        
        .box .col {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #ccc;
        }
        
        .box .col:last-child {
            border-right: none;
        }
        
        .box strong {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2>Employee Attendance View</h2>
        <hr class="my-4">
        
        <!-- Search Form -->
        <form action="" method="get">
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <label for="emp_name">Employee Name</label>
                    <?php include 'includes/emp_list.php'; ?>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="month">Month</label>
                    <select class="form-control" id="month" name="month">
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $monthValue = str_pad($i, 2, '0', STR_PAD_LEFT);
                            echo '<option value="' . $monthValue . '">' . date('F', mktime(0, 0, 0, $i, 1)) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="year">Year</label>
                    <input type="number" class="form-control" id="year" name="year" value="2023" min="2000" max="2099">
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Search</button>
                <button class="btn btn-primary" id="printButton">Print Report</button>
            </div>
        </form>
    </div>
    
    <div class="container mt-4 text-center">
        <h2>Attendance Summary</h2>
        <hr>
        <?php
        include 'includes/db_connection.php';
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['emp_name']) && isset($_GET['month']) && isset($_GET['year'])) {
            $empName = $_GET['emp_name'];
            $month = $_GET['month'];
            $year = $_GET['year'];
            
            // Fetch employee details based on the selected employee name
            $empDetailsQuery = "SELECT id, name, salary FROM emp WHERE name = ?";
            if ($stmt = $conn->prepare($empDetailsQuery)) {
                $stmt->bind_param("s", $empName);
                $stmt->execute();
                $stmt->bind_result($empId, $empName, $empSalary);
                $stmt->fetch();
                $stmt->close();
                
                // Combine month and year to create a valid date format
                $selectedDate = $year . "-" . $month . "-01";
                
                // Fetch attendance data based on employee ID and selected date
                $attendanceDataQuery = "SELECT date, status FROM atd 
                WHERE emp_id = ? 
                AND MONTH(date) = ? 
                AND YEAR(date) = ?";
                if ($stmt = $conn->prepare($attendanceDataQuery)) {
                    $stmt->bind_param("iss", $empId, $month, $year);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    // Calculate attendance summary
                    $totalAttendance = $result->num_rows;
                    $totalAbsents = 0;
                    while ($row = $result->fetch_assoc()) {
                        if ($row['status'] === 'A') {
                            $totalAbsents++;
                        }
                    }
                    
                    // Calculate total salary, total received, and remaining salary
                    $totalSalary = $empSalary;
                    $totalReceived = 0; // Fetch from emp_payment table (replace with actual column name)
                    // Fetch salary information from the sal table
                    $salaryQuery = "SELECT amount, date FROM sal WHERE emp_id = ? AND MONTH(date) = ? AND YEAR(date) = ?";
                    if ($stmt = $conn->prepare($salaryQuery)) {
                        $stmt->bind_param("iss", $empId, $month, $year);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $totalSalaryReceived = 0;
                        while ($row = $result->fetch_assoc()) {
                            $totalSalaryReceived += $row['amount'];
                        }
                    } else {
                        // Handle error if needed
                        echo '<p>Error fetching salary data.</p>';
                    }

                    $remainingSalary = $totalSalaryReceived;

                    // Deduct salary for every day after 4 days of absence
                    $allowedAbsenceDays = 4;
                    if ($totalAbsents > $allowedAbsenceDays) {
                        $absentDeductionPerDay = $empSalary / ($totalAttendance);
                        $absentDays = $totalAbsents - $allowedAbsenceDays;
                        $lessAmountByAbsents = $absentDays * $absentDeductionPerDay;
                        $remainingSalary = $totalSalary - $lessAmountByAbsents;
                    } else {
                        $absentDeductionPerDay = 0;
                        $absentDays = 0;
                        $lessAmountByAbsents = 0;
                    }

                    $remainingSalary = $totalSalary - $lessAmountByAbsents -$totalSalaryReceived;
                    
                    // Display attendance summary using the .box structure
                    echo '<div class="box">';
                    echo '<div class="row">';
                    echo '<div class="col"><strong>Employee Name</strong><span>' . $empName . '</span></div>';
                    echo '<div class="col"><strong>Total Attendance</strong><span>' . $totalAttendance . '</span></div>';
                    echo '<div class="col"><strong>Total Absents</strong><span>' . $totalAbsents . '</span></div>';
                    echo '<div class="col"><strong>Total Salary</strong><span>' . number_format($totalSalary, 2) . '</span></div>';
                    echo '<div class="col"><strong>Total Received</strong><span>' . number_format($totalSalaryReceived, 2) . '</span></div>';
                    echo '<div class="col"><strong>Less Amount</strong><span>' . number_format($lessAmountByAbsents, 2) . '</span></div>';
                    echo '<div class="col"><strong>Remaining Salary</strong><span>' . number_format($remainingSalary, 2) . '</span></div>';
                    echo '</div>';
                    echo '</div>';


                    
                } else {
                    echo '<p>Error fetching attendance data.</p>';
                }
            } else {
                echo '<p>Error fetching employee data.</p>';
            }
            
            $conn->close();
        }
        ?>
    </div>

    <div class="container mt-4 text-center">
    <h2>Attendance Table</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Ser No.</th>
                <th>Date</th>
                <th>Day</th>
                <th>Status</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch attendance data and display in the table
            include 'includes/db_connection.php';

            if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['emp_name']) && isset($_GET['month']) && isset($_GET['year'])) {
                $empName = $_GET['emp_name'];
                $month = $_GET['month'];
                $year = $_GET['year'];

                // Get employee ID based on the selected employee name
                $empIdQuery = "SELECT id FROM emp WHERE name = ?";
                if ($stmt = $conn->prepare($empIdQuery)) {
                    $stmt->bind_param("s", $empName);
                    $stmt->execute();
                    $stmt->bind_result($empId);
                    $stmt->fetch();
                    $stmt->close();

                    // Combine month and year to create a valid date format
                    $selectedDate = $year . "-" . $month . "-01";

                    // Fetch attendance data based on employee ID and selected date
                    $attendanceDataQuery = "SELECT date, status FROM atd 
                    WHERE emp_id = ? 
                    AND MONTH(date) = ? 
                    AND YEAR(date) = ?";
                    if ($stmt = $conn->prepare($attendanceDataQuery)) {
                        $stmt->bind_param("iss", $empId, $month, $year);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $serialNumber = 1; // Initialize serial number

                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $serialNumber . '</td>'; // Serial number
                            echo '<td>' . date('d-M', strtotime($row['date'])) . '</td>';
                            echo '<td>' . date('l', strtotime($row['date'])) . '</td>'; // Day of the week
                            echo '<td>' . ($row['status'] === 'P' ? 'Present' : 'Absent') . '</td>';

                            // Fetch salary information from the sal table
                            $salaryQuery = "SELECT amount, date FROM sal WHERE emp_id = ? AND date = ?";
                            if ($salaryStmt = $conn->prepare($salaryQuery)) {
                                $salaryStmt->bind_param("is", $empId, $row['date']);
                                $salaryStmt->execute();
                                $salaryResult = $salaryStmt->get_result();
                                if ($salaryRow = $salaryResult->fetch_assoc()) {
                                    echo '<td>' . number_format($salaryRow['amount'], 2) . '</td>';
                                } else {
                                    echo '<td>-</td>';
                                }
                                $salaryStmt->close();
                            } else {
                                // Handle error if needed
                                echo '<td colspan="2">Error fetching salary data.</td>';
                            }

                            echo '</tr>';

                            $serialNumber++; // Increment serial number
                        }
                    } else {
                        // Handle error if needed
                        echo '<tr><td colspan="5">Error fetching attendance data.</td></tr>';
                    }
                } else {
                    // Handle error if needed
                    echo '<tr><td colspan="5">Error fetching employee data.</td></tr>';
                }

                $conn->close();
            }
            ?>
        </tbody>
    </table>
</div>
    
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Print Button Click Handler
        $('#printButton').click(function() {
            openPrintTab();
        });

        // Function to Open a New Tab and Print the Report
        function openPrintTab() {
            var empName = "<?php echo $empName; ?>";
            var month = "<?php echo $month; ?>";
            var year = "<?php echo $year; ?>";
            var reportUrl = "includes/get_atd.php?emp_name=" + empName + "&month=" + month + "&year=" + year;
            window.open(reportUrl, '_blank');
        }
    });
</script>

</body>
</html>
