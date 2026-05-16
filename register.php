<?php
session_start();
require_once 'includes/connect.php';

$message = "";

// If already logged in, redirect them out
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'PERSONNEL') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $pass  = $_POST['password'];
    $cpass = $_POST['confirm_password'];

    if ($pass !== $cpass) {
        $message = "<div class='alert error'>Passwords do not match!</div>";
    } else {
        // 1. Check if email exists in tbluser
        $checkEmail = $conn->prepare("SELECT email FROM tbluser WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert error'>Email already registered.</div>";
        } else {
            try {
                // 2. Direct insertion to tbluser. tblpersonnel is no longer needed.
                $stmt = $conn->prepare("INSERT INTO tbluser (fname, lname, email, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $fname, $lname, $email, $pass);
                $stmt->execute();
                
                header("Location: login.php?msg=registered");
                exit();
            } catch (Exception $e) {
                $message = "<div class='alert error'>Error: " . $e->getMessage() . "</div>";
            }
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #d32f2f;
            --primary-hover: #b71c1c;
            --bg: #f8fafc;
            --text: #0f172a;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { 
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://educationsnapshots.com/wp-content/uploads/sites/4/2024/11/canada-international-school-canteen-9-1050x750-compact.jpg');
            background-size: cover;
            background-position: center;
            display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        
        .gateway-container { background: white; width: 100%; max-width: 450px; border-radius: 28px; padding: 40px; box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
        .brand-header { text-align: center; margin-bottom: 25px; }
        .brand-header h1 { font-size: 2.2rem; font-weight: 800; color: var(--primary); letter-spacing: -1px; line-height: 1.1; }
        .brand-header p { color: #64748b; font-size: 0.9rem; margin-top: 5px; }
        
        h2 { font-size: 1.35rem; font-weight: 800; margin-bottom: 20px; color: var(--text); text-align: center; }

        .alert { padding: 12px; border-radius: 10px; margin-bottom: 15px; font-size: 0.85rem; font-weight: 600; text-align: center; }
        .alert.error { background: #fee2e2; color: #991b1b; }
        .alert.success { background: #d1e7dd; color: #0f5132; }

        .row { display: flex; gap: 15px; }
        .row div { flex: 1; }

        label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: var(--text); }
        input { width: 100%; padding: 14px 16px; border-radius: 12px; border: 2px solid #f1f5f9; font-size: 0.95rem; margin-bottom: 18px; outline: none; transition: 0.2s; }
        input:focus { border-color: var(--primary); }
        
        .btn-register { width: 100%; background: var(--primary); color: white; padding: 14px; border-radius: 12px; border: none; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.2s; margin-top: 5px; }
        .btn-register:hover { background: var(--primary-hover); }
        
        .footer-links { text-align: center; margin-top: 20px; font-size: 0.85rem; color: #64748b; }
        .footer-links a { color: var(--primary); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="gateway-container">
    <div class="brand-header">
        <h1>WildCats<br>Price Checker</h1>
        <p>CIT-U Campus Utility Portal</p>
    </div>

    <h2>Store Registration</h2>
    
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
        
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="••••••••" required>
        
        <button type="submit" class="btn-register">Register Account</button>
    </form>

    <div class="footer-links">
        Already have an account? <a href="login.php">Sign In</a>
    </div>
</div>

</body>
</html>