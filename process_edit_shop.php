<?php
session_start();
require_once 'includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PERSONNEL') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sid = mysqli_real_escape_string($conn, $_POST['sid']);
    $sname = mysqli_real_escape_string($conn, $_POST['sname']);

    if (!empty($sid) && !empty($sname)) {
        // Update the shop name in tblshop
        $sql = "UPDATE tblshop SET sname = '$sname' WHERE sid = '$sid'";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: manage_shop.php?msg=shop_updated");
            exit();
        } else {
            echo "Error updating shop: " . mysqli_error($conn);
        }
    }
} else {
    header("Location: manage_shop.php");
    exit();
}
?>