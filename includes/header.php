<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BillSys</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="managerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Management
                    </a>
                    <div class="dropdown-menu" aria-labelledby="managerDropdown">
                        <a class="dropdown-item" href="prod.php">Add Products</a>
                        <a class="dropdown-item" href="m_prod.php">Manage Products</a>
                        <a class="dropdown-item" href="emp.php">Add Employees</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="receiptDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Receipt
                    </a>
                    <div class="dropdown-menu" aria-labelledby="receiptDropdown">
                        <a class="dropdown-item" href="rec.php">Create Receipt</a>
                        <a class="dropdown-item" href="m_rec.php">Manage Receipt</a>
                        <a class="dropdown-item" href="u_rec.php">Update Receipt</a>
                        <a class="dropdown-item" href="bal.php">Receiving Balance</a>
                        <a class="dropdown-item" href="reca.php">Receiving Amount</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="attendanceDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Attendance
                    </a>
                    <div class="dropdown-menu" aria-labelledby="attendanceDropdown">
                        <a class="dropdown-item"  href="atd.php">Add Attendance</a>
                        <a class="dropdown-item"  href="sal.php">Pay Salary</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="exp.php">Expenses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pend.php">Pending Amounts</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="attendanceDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Reports
                    </a>
                    <div class="dropdown-menu" aria-labelledby="reportDropdown">
                        <a class="dropdown-item"  href="day.php">Daily Report</a>
                        <a class="dropdown-item"  href="qty.php">Inventory Report</a>
                        <a class="dropdown-item"  href="m_atd.php">Salary Sheet</a>
                        <a class="dropdown-item"  href="m_reca.php">Receiving Report</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</body>
</html>
