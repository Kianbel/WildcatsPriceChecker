<?php
require_once 'includes/connect.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $pass  = $_POST['password'];
    $cpass = $_POST['confirm_password'];
    $type  = $_POST['user_type'];

    if ($pass !== $cpass) {
        $message = "<div class='alert error'>Passwords do not match!</div>";
    } else {
        $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert error'>Email already registered.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, password, usertype) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fname, $lname, $email, $pass, $type);
            
            if ($stmt->execute()) {
                $message = "<div class='alert success'>Account created! <a href='index.php'>Sign In</a></div>";
            } else {
                $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildCats - Create Account</title>
    <link href="css/register.css" rel="stylesheet">
</head>
<body>

<div class="register-card">
    <div class="header-box">
        <h1>WildCats<br>Price Checker</h1>
    </div>
    
    <h2>Create account</h2>
    
    <?php echo $message; ?>

    <form method="POST" action="">
        <div class="row">
            <div>
                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Juan" required>
            </div>
            <div>
                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Dela Cruz" required>
            </div>
        </div>

        <label>Email Address</label>
        <input type="email" name="email" placeholder="juan.delacruz@cit.edu" required>
        
        <label>User Type</label>
        <select name="user_type" required>
            <option value="" disabled selected>Choose type</option>
            <option value="Student">Student</option>
            <option value="Personnel">Personnel</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="••••••••" required>
        
        <button type="submit" class="btn-register">Register</button>
    </form>

    <div class="signin-link">
        Already have an account? <a href="login.php">Sign In</a>
    </div>
</div>

</body>
</html>