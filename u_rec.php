<?php 
include 'includes/header.php'; 
include 'includes/db_connection.php'; 

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
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6">
            <h2 class="text-center">Retrieve Receipt</h2>
            <hr class="my-4">
            <form method="post" id="retrieveForm">
                <div class="form-group">
                    <label for="invoiceNumber">Invoice #</label>
                    <input type="text" class="form-control" id="invoiceNumber" name="invoiceNumber">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>


    <div class="container mt-4 text-center">
        <h2>Update Receipt</h2>
        <hr class="my-4">
        <form action="includes/upd_rec.php" method="post">
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="cust_name">Customer Name</label>
                        <input type="cust_name" class="form-control" id="cust_name" name="cust_name">
                    </div>
                    <div class="col">
                        <label for="proj_name">Project Name</label>
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
                        <input type="text" class="form-control" id="invoice" name="invoice" readonly required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Width</th>
                            <th>Height</th>
                            <th>Quantity</th>
                            <th>Sq ft</th>
                            <th>Material</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="ReceiptRows">
                        <tr>
                            <td><input type="number" step="0.01" class="form-control width" name="width[]" required></td>
                            <td><input type="number" step="0.01" class="form-control height" name="height[]" required></td>
                            <td><input type="number" class="form-control qty" name="qty[]" required></td>
                            <td><input type="number" step="0.01" class="form-control sqft" name="sqft[]" readonly required></td>
                            <td>
                                <?php include 'includes/prod_list.php'; ?>
                            </td>
                            <td><input type="number" step="0.01" class="form-control total" name="total[]" readonly required></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" id="addRow">Add Row</button>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="dis">Discount</label>
                        <input type="number" step="0.01" class="form-control dis" id="dis" name="dis">
                    </div>
                    <div class="col">
                        <label for="g_total">Grand Total</label>
                        <input type="number" class="form-control g_total" id="g_total" name="g_total" readonly>
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
                <button type="submit" class="btn btn-primary">Update</button>
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
            var rowTotal = parseFloat($(this).val());
            if (!isNaN(rowTotal)) {
                grandTotal += rowTotal;
            }
        });
        $("#g_total").val(grandTotal.toFixed(2));
    }

    
    // Calculate total whenever height, width, or quantity changes in a row
    $("#ReceiptRows").on("input", ".height, .width, .qty", function() {
        var row = $(this).closest("tr");
        var height = parseFloat(row.find(".height").val()) || 0;
        var width = parseFloat(row.find(".width").val()) || 0;
        var qty = parseInt(row.find(".qty").val()) || 0;
        var totalElement = row.find(".total");

        // Check if height, width, and qty are valid numbers
        if (!isNaN(height) && !isNaN(width) && !isNaN(qty)) {
            var sqft = (height * width * qty).toFixed(2);
            row.find(".sqft").val(sqft);
        } else {
            // Handle invalid input gracefully, e.g., clear the sqft field
            row.find(".sqft").val("");
        }

        calculateGrandTotal();
    });

    // Calculate discount and update total when Enter key is pressed in the "dis" field
    $(".dis").on("keypress", function(event) {
        if (event.which === 13) { // Check if Enter key (key code 13) is pressed
            event.preventDefault(); // Prevent the default behavior of Enter key (form submission)
            var discount = parseFloat($(this).val()) || 0;
            var grandTotal = parseFloat($("#g_total").val()) || 0;
            var newGrandTotal = (grandTotal - discount).toFixed(2);
            $("#g_total").val(newGrandTotal);
            calculateBalance(); // Calculate the balance when the discount changes
        }
    });


    // Function to calculate balance based on grand total and advance
    function calculateBalance() {
        var grandTotal = parseFloat($("#g_total").val()) || 0;
        var advance = parseFloat($("#adv").val()) || 0;
        var balance = grandTotal - advance;
        $("#bal").val(balance.toFixed(2));
    }  

    // Calculate total whenever product selection changes
    $("#ReceiptRows").on("change", ".product", function() {
        var row = $(this).closest("tr");
        var sqft = parseFloat(row.find(".sqft").val()) || 0;
        var selectedProduct = $(this).val();

        // Use AJAX to fetch the product rate from the server
        $.ajax({
            url: "includes/get_product_rate.php",
            method: "GET",
            data: { product: selectedProduct },
            success: function(response) {
                var productRate = parseFloat(response) || 0;
                if (!isNaN(sqft) && !isNaN(productRate)) {
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

    // Calculate balance whenever advance input changes
    $("#adv").on("input", function() {
        var grandTotal = parseFloat($("#g_total").val()) || 0;
        var advance = parseFloat($(this).val()) || 0;
        if (!isNaN(grandTotal) && !isNaN(advance)) {
            var balance = grandTotal - advance;
            $("#bal").val(balance.toFixed(2));
        }
    });

    // Add row button functionality
    $("#addRow").click(function() {
        var newRow = `
            <tr>
                <td><input type="number" step="0.01" class="form-control width" name="width[]"></td>
                <td><input type="number" step="0.01" class="form-control height" name="height[]"></td>
                <td><input type="number" class="form-control qty" name="qty[]" required></td>
                <td><input type="number" step="0.01" class="form-control sqft" name="sqft[]" readonly></td>
                <td>
                    <?php include 'includes/prod_list.php'; ?>
                </td>
                <td><input type="number" step="0.01" class="form-control total" name="total[]" readonly required data-initial-total="0" data-discount="0"></td>
            </tr>
        `;
        $("#ReceiptRows").append(newRow);
    });
});
</script>
<script>
    // Handle form submission for retrieving information
$("#retrieveForm").submit(function(event) {
    event.preventDefault(); // Prevent the default form submission behavior
    
    var invoiceNumber = $("#invoiceNumber").val();
    
    // Use AJAX to fetch data based on the entered invoice number
    $.ajax({
        url: "includes/retrieve_invoice.php", // Create a PHP script to handle the database query
        method: "POST",
        data: { invoiceNumber: invoiceNumber },
        success: function(response) {
            // Parse the response and fill the fields with retrieved data
            var data = JSON.parse(response);
            if (data) {
                $("#cust_name").val(data.cust_name);
                $("#proj_name").val(data.proj_name);
                $("#date").val(data.date);
                $("#phone").val(data.phone);
                $("#invoice").val(data.id);
                $("#dis").val(data.disc);
                $("#adv").val(data.advance);
                $("#bal").val(data.balance);
                $("#g_total").val(data.grand_total);
                
                // Clear and recreate the table rows with retrieved data from 'rect' table
                $("#ReceiptRows").empty();
                data.rect.forEach(function(rowData) {
                    addTableRow(rowData, rowData.prod_id);

                });
            } else {
                // Handle the case when the invoice number is not found
                alert("Invoice number not found.");
            }
        },
        error: function() {
            alert("Error retrieving data. Please try again.");
        }
    });
});
function addTableRow(rowData, productId) {
    var newRow = `
        <tr>
            <td><input type="number" step="0.01" class="form-control width" name="width[]" value="${rowData.width}"></td>
            <td><input type="number" step="0.01" class="form-control height" name="height[]" value="${rowData.height}"></td>
            <td><input type="number" class="form-control qty" name="qty[]" value="${rowData.qty}" required></td>
            <td><input type="number" step="0.01" class="form-control sqft" name="sqft[]" value="${rowData.sq_ft}" readonly required></td>
            <td>
                <select class="form-control product" name="product[]" required>
                    <!-- Product options will be populated dynamically -->
                </select>
            </td>
            <td><input type="number" step="0.01" class="form-control total" name="total[]" value="${rowData.total}" readonly required></td>
        </tr>
    `;
    $("#ReceiptRows").append(newRow);

    // Use AJAX to fetch and populate product options based on the provided product ID
    $.ajax({
        url: "includes/prod_list.php",
        method: "GET",
        data: { prodId: productId }, // Pass the product ID here
        success: function(response) {
            // Populate the product select field with options
            var selectField = $("#ReceiptRows tr:last-child select.product");
            selectField.empty().append(response);
            selectField.val(rowData.product); // Set the selected product
        },
        error: function() {
            // Handle the error if necessary
        }
    });
}


</script>
</body>
</html>