<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
        
        // Fetch attendance data based on employee ID, month, and year
        $attendanceDataQuery = "SELECT date, status FROM atd 
                                WHERE emp_id = ? 
                                AND MONTH(date) = ? 
                                AND YEAR(date) = ?";
        
        if ($stmt = $conn->prepare($attendanceDataQuery)) {
            $stmt->bind_param("iis", $empId, $month, $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $attendanceData = array();
            
            while ($row = $result->fetch_assoc()) {
                $attendanceData[] = $row;
            }
            
            echo json_encode($attendanceData);
        } else {
            // Handle error if needed
            echo "Database error.";
        }
    } else {
        // Handle error if needed
    }
    
    $conn->close();
}
?>
