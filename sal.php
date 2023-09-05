<?php include 'includes/header.php'?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Salary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2>Employee Salary</h2>
        <hr>
        <form action="includes/save_sal.php" method="post">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="emp_name">Employee Name</label>
                    <?php include 'includes/emp_list.php'; ?>
                </div>
                <div class="form-group col-md-4">
                    <label for="date">Salary Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="amount">Salary Amount</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Insert Salary</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
