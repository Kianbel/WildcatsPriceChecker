<?php
session_start();
require_once 'includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $sid = mysqli_real_escape_string($conn, $_POST['sid']);
    $description = mysqli_real_escape_string($conn, $_POST['shop_description']);

    // Update the description in tblshop
    $query = "UPDATE tblshop SET shop_description = '$description' WHERE sid = '$sid'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: manage_shop.php?msg=description_updated");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    header("Location: manage_shop.php");
}
?>