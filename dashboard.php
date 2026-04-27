<?php
session_start();

// Security check: if session is empty, redirect to login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildCats - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 350px; }
        h1 { color: #d32f2f; margin-bottom: 20px; }
        .info-box { text-align: left; background: #fafafa; padding: 20px; border-radius: 10px; border: 1px solid #eee; }
        .label { font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; }
        .value { font-size: 16px; color: #333; margin-bottom: 15px; }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; background: #d32f2f; color: white; }
        .btn-logout { display: block; margin-top: 25px; color: #666; text-decoration: none; font-size: 14px; }
        .btn-logout:hover { color: #d32f2f; }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome Home!</h1>
    
    <div class="info-box">
        <p class="label">Full Name</p>
        <p class="value"><?php echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?></p>

        <p class="label">Email Address</p>
        <p class="value"><?php echo $_SESSION['email']; ?></p>

        <p class="label">Account Type</p>
        <span class="badge"><?php echo $_SESSION['usertype']; ?></span>
    </div>

    <a href="logout.php" class="btn-logout">Logout</a>
</div>

</body>
</html>