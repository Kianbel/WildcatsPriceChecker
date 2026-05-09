<?php
session_start();
require_once 'includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PERSONNEL') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemid = mysqli_real_escape_string($conn, $_POST['itemid']);
    $itemname = mysqli_real_escape_string($conn, $_POST['itemname']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    if (!empty($itemid) && !empty($itemname) && !empty($price)) {
        // Update query using itemid as the unique identifier
        $sql = "UPDATE tblitem SET itemname = '$itemname', price = '$price' WHERE itemid = '$itemid'";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: manage_shop.php?msg=item_updated");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
}
?>