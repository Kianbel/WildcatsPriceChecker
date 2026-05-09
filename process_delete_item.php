<?php
session_start();
require_once 'includes/connect.php';

// Security check: Ensure only PERSONNEL can perform this action
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PERSONNEL') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $itemid = mysqli_real_escape_string($conn, $_GET['id']);

    // SQL: Remove the specific item record
    $sql = "DELETE FROM tblitem WHERE itemid = '$itemid'";

    if (mysqli_query($conn, $sql)) {
        // Redirect back with a success message
        header("Location: manage_shop.php?msg=item_deleted");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // If no ID is provided, redirect back
    header("Location: manage_shop.php");
    exit();
}
?>