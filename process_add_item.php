<?php
session_start();
require_once 'includes/connect.php'; // Ensure this matches your dashboard's connection file

// Security: Only allow Personnel to add items
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PERSONNEL') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $itemname = mysqli_real_escape_string($conn, $_POST['itemname']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $sid = mysqli_real_escape_string($conn, $_POST['sid']);

    // Validation: Ensure fields aren't empty
    if (!empty($itemname) && !empty($price) && !empty($sid)) {
        
        // SQL: Insert the item linked to the specific shop ID (sid)
        $sql = "INSERT INTO tblitem (itemname, price, sid) VALUES ('$itemname', '$price', '$sid')";
        
        if (mysqli_query($conn, $sql)) {
            // Success! Redirect back to management page with a success message
            header("Location: manage_shop.php?msg=item_added");
            exit();
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Please fill in all fields.";
    }
} else {
    // If someone tries to access this file directly without posting, send them back
    header("Location: manage_shop.php");
    exit();
}
?>