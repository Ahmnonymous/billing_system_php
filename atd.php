<?php include 'includes/header.php';?>
<!-- attendance_form.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Daily Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2>Add Daily Attendance</h2>
        <hr class="my-4">
        
        <!-- Attendance Input Form -->
        <form action="includes/save_atd.php" method="post">
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="emp_id">Employee</label>
                    <?php include 'includes/emp_list.php'; ?>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="P">Present</option>
                        <option value="A">Absent</option>
                    </select>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Add Attendance</button>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
