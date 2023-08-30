<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance View</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
</head>
<body>
<?php
include 'db_connection.php';

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

        $selectedDate = $year . "-" . $month . "-01";

        $attendanceDataQuery = "SELECT date, status FROM atd 
        WHERE emp_id = ? 
        AND MONTH(date) = ? 
        AND YEAR(date) = ?";
        if ($stmt = $conn->prepare($attendanceDataQuery)) {
            $stmt->bind_param("iss", $empId, $month, $year);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalAttendance = $result->num_rows;
            $totalAbsents = 0;
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] === 'A') {
                    $totalAbsents++;
                }
            }

            $totalSalary = $empSalary;
            $totalReceived = 0;

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
                echo '<p>Error fetching salary data.</p>';
            }

            $remainingSalary = $totalSalaryReceived;

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

            $remainingSalary = $totalSalary - $lessAmountByAbsents - $totalSalaryReceived;
            echo '<div class="container mt-4 text-center">';
            echo '<h2><strong><i>'. $empName . ' Salary ' . date('F', strtotime($selectedDate)) . ' ' . $year. '</i></strong></h2><hr>';
            echo '</div>';

            // Display attendance summary using the .box structure
            echo '<div class="container mt-4 text-center">';
            echo '<div class="box">';
            echo '<div class="row">';
            echo '<div class="col"><strong>Total Attendance</strong><span>' . $totalAttendance . '</span></div>';
            echo '<div class="col"><strong>Total Absents</strong><span>' . $totalAbsents . '</span></div>';
            echo '<div class="col"><strong>Total Salary</strong><span>' . number_format($totalSalary, 2) . '</span></div>';
            echo '<div class="col"><strong>Total Received</strong><span>' . number_format($totalSalaryReceived, 2) . '</span></div>';
            echo '<div class="col"><strong>Less Amount</strong><span>' . number_format($lessAmountByAbsents, 2) . '</span></div>';
            echo '<div class="col"><strong>Rem. Salary</strong><span>' . number_format($remainingSalary, 2) . '</span></div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            echo '<div class="container mt-4 text-center">';
            echo '<table class="table">';
            echo '<thead><tr>';
            echo '<th>Ser No.</th>';
            echo '<th>Date</th>';
            echo '<th>Day</th>';
            echo '<th>Status</th>';
            echo '<th>Salary</th>';
            echo '</tr></thead>';
            echo '<tbody>';

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
                            echo '<td>' . number_format($salaryRow['amount'], 2) . '</td>'; // Display salary amount
                        } else {
                            echo '<td>-</td>';
                        }
                        $salaryStmt->close();
                    } else {
                        // Handle error if needed
                        echo '<td colspan="2">Error fetching salary data.</td>';
                    }
                
                    // Remove this extra </tr> tag
                    $serialNumber++; // Increment serial number
                }
                

            } else {
                // Handle error if needed
                echo '<tr><td colspan="4">Error fetching attendance data.</td></tr>';
            }

            echo '</tbody></table></div>';
        } else {
            echo 'Error fetching attendance data.';
        }
    } else {
        echo 'Error fetching employee data.';
    }

    $conn->close();
}
?>
</body>
</html>

