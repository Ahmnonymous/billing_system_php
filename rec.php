<?php 
include 'includes/header.php'; 
include 'includes/db_connection.php'; 

// Query to retrieve the maximum id from recm table
$maxIdQuery = "SELECT MAX(id)+1 AS max_id FROM recm";
$maxIdResult = $conn->query($maxIdQuery);

$maxId = 1; // Default to 1 if there are no records yet

if ($maxIdResult->num_rows > 0) {
    $row = $maxIdResult->fetch_assoc();
    $maxId = $row["max_id"] ; // Increment by 1 for the next invoice
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BillSys - Receipt Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2>Create Receipt</h2>
        <hr class="my-4">
        <form action="includes/create_rec.php" method="post">
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="cust_name">Customer Name</label>
                        <input type="cust_name" class="form-control" id="cust_name" name="cust_name">
                    </div>
                    <div class="col">
                        <label for="project">Project Name</label>
                        <input type="proj_name" class="form-control" id="proj_name" name="proj_name">
                    </div>
                    <div class="col">
                        <label for="phone">Phone No.</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col">
                        <label for="invoice">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice" name="invoice" value="<?php echo $maxId; ?>" readonly required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S No.</th>
                            <th>Width</th>
                            <th>Height</th>
                            <th>Quantity</th>
                            <th>Sq ft</th>
                            <th>Material</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="ReceiptRows">
                        <tr>
                            <td><input type="text" class="form-control" name="ser_no[]" value="1" readonly required></td>
                            <td><input type="number" step="0.01" class="form-control width" name="width[]" required></td>
                            <td><input type="number" step="0.01" class="form-control height" name="height[]" required></td>
                            <td><input type="number" class="form-control qty" name="qty[]" required></td>
                            <td><input type="number" step="0.01" class="form-control sqft" name="sqft[]" readonly required></td>
                            
                            <td>
                                <?php include 'includes/prod_list.php'; ?>
                            </td>
                            <td><input type="number" step="0.01" class="form-control dis" name="dis[]"></td>
                            <td><input type="number" step="0.01" class="form-control total" name="total[]" readonly required></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" id="addRow">Add Row</button>
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
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {

        function calculateGrandTotal() {
            var grandTotal = 0;
            $(".total").each(function() {
                var rowTotal = parseFloat($(this).closest("tr").find(".total").val());
                if (!isNaN(rowTotal)) {
                    grandTotal += rowTotal;
                }
            });
            $("#g_total").val(grandTotal.toFixed(2));
        }

        // Add row button functionality
        let serialNumber = 2; 
        $("#addRow").click(function() {
            var newRow = `
                <tr>
                    <td><input type="text" class="form-control" name="ser_no[]" readonly value="${serialNumber}"></td>
                    <td><input type="number" step="0.01" class="form-control width" name="width[]"></td>
                    <td><input type="number" step="0.01" class="form-control height" name="height[]"></td>
                    <td><input type="number"  class="form-control qty" name="qty[]" required></td>
                    <td><input type="number" step="0.01" class="form-control sqft" name="sqft[]" readonly></td>
                    
                    <td>
                        <?php include 'includes/prod_list.php'; ?>
                    </td>
                    <td><input type="number" step="0.01" class="form-control dis" name="dis[]"></td>
                    <td><input type="number" step="0.01" class="form-control total" name="total[]" readonly></td>
                </tr>
            `;
            serialNumber++;
            $("#ReceiptRows").append(newRow);
        });

       // Calculate sqft whenever height, width, or quantity changes
        $("#ReceiptRows").on("input", ".height, .width, .qty", function() {
            var row = $(this).closest("tr");
            var height = parseFloat(row.find(".height").val());
            var width = parseFloat(row.find(".width").val());
            var qty = parseInt(row.find(".qty").val());

            // Check if height, width, and qty are valid numbers
            if (!isNaN(height) && !isNaN(width) && !isNaN(qty)) {
                var sqft = (height * width * qty).toFixed(2);
                row.find(".sqft").val(sqft);
                calculateGrandTotal();
            }
        });

        // Calculate total whenever discount changes
        $("#ReceiptRows").on("input", ".dis", function() {
            var row = $(this).closest("tr");
            var discount = parseFloat($(this).val());
            var total = parseFloat(row.find(".total").val());

            if (!isNaN(total) && !isNaN(discount)) {
                var newTotal = total - discount;
                row.find(".total").val(newTotal.toFixed(2));
                calculateGrandTotal();
            }
        });


        // Calculate total whenever product selection changes
        $("#ReceiptRows").on("change", ".product", function() {
            var row = $(this).closest("tr");
            var sqft = parseFloat(row.find(".sqft").val());
            var selectedProduct = $(this).val();

            // Use AJAX to fetch the product rate from the server
            $.ajax({
                url: "includes/get_product_rate.php",
                method: "GET",
                data: { product: selectedProduct },
                success: function(response) {
                    var productRate = parseFloat(response);
                    if (!isNaN(sqft) && !isNaN(productRate) ) {
                        var total = (sqft * productRate).toFixed(2);
                        row.find(".total").val(total);
                        calculateGrandTotal();
                    }
                },
                error: function() {
                    row.find(".total").val("");
                    calculateGrandTotal();
                }
            });
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