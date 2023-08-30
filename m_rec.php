<?php
include 'includes/header.php';
include 'includes/db_connection.php'; // Your database connection

// Get search parameters if submitted
$invoice = isset($_GET['invoice']) ? $_GET['invoice'] : '';
$customer = isset($_GET['customer']) ? $_GET['customer'] : '';
$phone = isset($_GET['phone']) ? $_GET['phone'] : '';

// Construct the SQL query with optional search parameters
$sql = "SELECT recm.id, recm.date, recm.invoice, cust.name AS customer_name, recm.phone, recm.balance
        FROM recm
        INNER JOIN cust ON recm.cust_id = cust.id
        WHERE recm.invoice LIKE '%$invoice%'
        AND cust.name LIKE '%$customer%'
        AND recm.phone LIKE '%$phone%'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts View</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 text-center">
        <h2>Manage Receipts</h2>
        <hr class="my-4">
        
        <!-- Search Form -->
        <form action="" method="get">
        <div class="form-row">
                <div class="col-md-4 mb-2">
                    <label for="invoice">Invoice</label>
                    <input type="text" class="form-control" id="invoice" name="invoice">
                </div>
                <div class="col-md-4 mb-2">
                    <label for="customer">Customer Name</label>
                    <input type="text" class="form-control" id="customer" name="customer">
                </div>
                <div class="col-md-4 mb-2">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>

        </form>
        <br>
        
        <div class="container mt-4 text-center">
    <h2>Created Receipts</h2><br>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['invoice'] ?></td>
                    <td><?= $row['customer_name'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['balance'] ?></td>
                    <td>
                        <a href="#" class="details-link" data-toggle="modal" data-target="#detailsModal"
                            data-id="<?= $row['id'] ?>">Details</a>
                        <a href="#" class="delete-link" data-id="<?= $row['id'] ?>">Delete</a>
                        <a href="#" class="print-link" data-id="<?= $row['id'] ?>">Print</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>

    <!-- Modal for Details -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Add "modal-lg" class to make it wider -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Receipt Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <!-- Content from rect table will be loaded here using AJAX -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        // Handle "Delete" button click
        $(".delete-link").click(function () {
            var recmId = $(this).data("id");

            if (confirm("Are you sure you want to delete this record?")) {
                // Use AJAX to delete rect and recm records
                $.ajax({
                    type: "POST",
                    url: "includes/delete_rect_recm.php", // Create a PHP file to handle deletion
                    data: { id: recmId },
                    success: function () {
                        // Reload the page after deletion
                        window.location.reload();
                    }
                });
            }
        });

// Handle "Print" button click
$(".print-link").click(function () {
    var recmId = $(this).data("id");
    
    // Use AJAX to fetch the printable data
    $.ajax({
        type: "GET",
        url: "includes/get_printable_data.php", // Modify this URL to the actual path
        data: { id: recmId },
        dataType: 'json', // Expect JSON response
        success: function (data) {
            // Construct the printable content
            var printableContent = '<!DOCTYPE html><html><head>';
            printableContent += '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';
            printableContent += '<style>';
            printableContent += '.container { max-width: 800px; margin: 20px auto; padding: 20px; }';
            printableContent += '.row { display: flex; justify-content: space-between; }';
            printableContent += '.col { text-align: center; padding: 10px; margin-bottom: 10px; flex: 1; }';
            printableContent += '.col span { display: block; border: 1px solid #ccc; padding: 5px; margin:5px;}';
            printableContent += 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
            printableContent += 'th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }';
            printableContent += '.totals { text-align: center; margin-top: 20px; }';
            printableContent += '</style>';
            printableContent += '</head><body><div class="container">';
            
            // Header
            printableContent += '<h2 class="text-center"><i><strong>RECEIPT</strong></i></h2><hr><br>';
            
            // Customer info
            printableContent += '<div class="box">';
            printableContent += '<div class="row">';
            printableContent += '<div class="col"><strong>Customer Name</strong><span>' + data.recm.customer_name + '</span></div>';
            printableContent += '<div class="col"><strong>Project</strong><span>' + data.recm.project_name + '</span></div>';
            printableContent += '<div class="col"><strong>Phone #</strong><span>' + data.recm.phone + '</span></div>';
            printableContent += '</div>';
            printableContent += '</div>';
            
            // Date and invoice
            printableContent += '<div class="box">';
            printableContent += '<div class="row">';
            printableContent += '<div class="col"><strong>Date</strong><span>' + data.recm.date + '</span></div>';
            printableContent += '<div class="col"><strong>Invoice</strong><span>' + data.recm.invoice + '</span></div>';
            printableContent += '</div>';
            printableContent += '</div>';
            
            // Rect details
            printableContent += '<div class="box">';
            printableContent += '<table class="table">';
            printableContent += '<tr><th>No.</th><th>Material</th><th>Height</th><th>Width</th><th>Sq Ft</th><th>Total</th></tr>';
            $.each(data.rect_details, function (index, rect) {
                var serialNo = index + 1;
                printableContent += '<tr><td>' + serialNo + '</td><td>' + rect.name + '</td><td>' + rect.height + '</td><td>' + rect.width + '</td><td>' + rect.sq_ft + '</td><td>' + rect.total + '</td></tr>';
            });
            printableContent += '</table>';
            printableContent += '</div>';
            
            // Recm totals
            printableContent += '<div class="box">';
            printableContent += '<div class="row">';
            printableContent += '<div class="col"><strong>Grand Total</strong><span>' + data.recm.grand_total + '</span></div>';
            printableContent += '<div class="col"><strong>Advance</strong><span>' + data.recm.advance + '</span></div>';
            printableContent += '<div class="col"><strong>Balance</strong><span>' + data.recm.balance + '</span></div>';
            printableContent += '</div>';
            printableContent += '</div>';
            
            printableContent += '</div></body></html>';
            
            // Create a new window for the printable content
            var printWindow = window.open('', '_blank');
            
            // Write the printable content to the new window
            printWindow.document.open();
            printWindow.document.write(printableContent);
            printWindow.document.close();
            
            // Trigger the print dialog
            //printWindow.print();
        }
    });
});


        // Handle "Details" link click
        $(".details-link").click(function () {
            var recmId = $(this).data("id");

            // Use AJAX to fetch and display details from rect table
            $.ajax({
                type: "GET",
                url: "includes/get_rect.php", // Create a PHP file to fetch details from rect table
                data: { id: recmId },
                success: function (data) {
                    $("#detailsModalBody").html(data);
                }
            });
        });
    });
</script>

</body>
</html>
       
