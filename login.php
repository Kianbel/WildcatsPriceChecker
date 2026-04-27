<?php
session_start();
require_once 'includes/connect.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password == $row['password']) {
            $message = "<div class='alert success'>Login Successful! Redirecting...</div>";
        } else {
            $message = "<div class='alert error'>Invalid Password.</div>";
        }
    } else {
        $message = "<div class='alert error'>User not found.</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildCats - Log In</title>
    <link href="css/login.css" rel="stylesheet">
</head>
<body>

<div class="login-card">
    <div class="header-box">
        <h1>WildCats<br>Price Checker</h1>
    </div>
    
    <h2>Log In</h2>
    
    <?php echo $message; ?>

    <form method="POST" action="">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="example@domain.com" required>
        
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
        
        <button type="submit" class="btn-login">Log In</button>
    </form>

    <div class="register-link">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</div>

</body>
</html>