<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BillSys - Receiving Amount</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-4 text-center">
    <h2>Receiving Amount</h2>
        <hr>
        <!-- Invoice Input Form -->
        <form method="post" class="d-inline-block">
            <div class="form-group">
                <label for="invoiceId">Invoice No.</label>
                <input type="number" class="form-control" id="invoiceId" name="invoiceId" required>
            </div>
            <button type="submit" class="btn btn-primary" name="fetchDetails" id="fetchDetails">Fetch Details</button>

        </form>
    </div>

    <div class="container mt-4 text-center">
        <!-- Receipt Creation Form -->
        <form action="includes/update_rec.php" method="post">
            <div class="form-group">
                <div class="row">
                    <div class="col">
                    <label for="id">Invoice No.</label>
                <input type="number" class="form-control" id="id" name="id" required readonly>
                    </div>
                    <div class="col">
                        <label for="cust_name">Customer Name</label>
                        <input type="text" class="form-control" id="cust_name" name="cust_name" readonly>
                    </div>
                    <div class="col">
                        <label for="proj_name">Project Name</label>
                        <input type="text" class="form-control" id="proj_name" name="proj_name" readonly>
                    </div>
                    <div class="col">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" readonly>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="g_total">Grand Total</label>
                        <input type="number" class="form-control" id="g_total" name="g_total" readonly>
                    </div>
                    <div class="col">
                        <label for="adv">Advance</label>
                        <input type="number" class="form-control" id="adv" name="adv" required>
                    </div>
                    <div class="col">
                        <label for="bal">Balance</label>
                        <input type="number" class="form-control" id="bal" name="bal" required>
                    </div>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" name="updateReceipt">Update</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to fetch data based on invoice ID
            function fetchRecmData(invoiceId) {
                // You need to implement an endpoint on your server that fetches data based on the invoiceId
                $.ajax({
                    url: 'includes/fetch_recm_data.php', // Replace with your server endpoint
                    method: 'POST',
                    data: { invoiceId: invoiceId },
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            // Populate the form fields with data from the response
                            $('#id').val(data.id);
                            $('#cust_name').val(data.cust_name);
                            $('#proj_name').val(data.proj_name);
                            $('#date').val(data.date);
                            $('#g_total').val(data.grand_total);
                            $('#bal').val(data.balance);
                            $('#adv').val(data.advance);
                        } else {
                            // Handle the case where no data is found
                            alert('Invoice not found.');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors here
                        console.error(error);
                    }
                });
            }

            // Event listener for the form submit
            $('#fetchDetails').on('click', function(e) {
                e.preventDefault(); // Prevent the form from submitting normally

                // Get the invoice ID input value
                var invoiceId = $('#invoiceId').val();

                // Call the fetchRecmData function to fetch data
                fetchRecmData(invoiceId);
            });

            // Calculate grand total whenever .total input changes
            $("#ReceiptRows").on("input", ".total", function() {
                calculateGrandTotal();
            });

            // Calculate balance whenever advance input changes
            $("#adv").on("input", function() {
                var grandTotal = parseFloat($("#g_total").val());
                var advance = parseFloat($(this).val());
                if (!isNaN(grandTotal) && !isNaN(advance)) {
                    var balance = grandTotal - advance;
                    $("#bal").val(balance.toFixed(2));
                }
            });
        });
    </script>
</body>
</html>
