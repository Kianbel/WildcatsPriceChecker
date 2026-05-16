<?php
session_start();
require_once 'includes/connect.php'; 

$message = "";

// If already logged in, skip this page
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'PERSONNEL') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Select account entry matching email - usertype column removed
    $stmt = $conn->prepare("SELECT accid, fname, lname, password FROM tbluser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if ($password == $row['password']) {
            // Set Personnel session
            $_SESSION['user_id'] = $row['accid'];
            $_SESSION['user_name'] = $row['fname'] . " " . $row['lname'];
            $_SESSION['user_role'] = "PERSONNEL";

            header("Location: dashboard.php"); 
            exit();
        } else {
            $message = "<div class='alert error' style='background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px; margin-bottom:15px; font-size:0.85rem; font-weight:600;'>Invalid Password.</div>";
        }
    } else {
        $message = "<div class='alert error' style='background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px; margin-bottom:15px; font-size:0.85rem; font-weight:600;'>Personnel account not found.</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildCats - Access Gateway</title>
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
        .brand-header { text-align: center; margin-bottom: 30px; }
        .brand-header h1 { font-size: 2.2rem; font-weight: 800; color: var(--primary); letter-spacing: -1px; line-height: 1.1; }
        .brand-header p { color: #64748b; font-size: 0.9rem; margin-top: 5px; }
        
        .guest-section { border-bottom: 2px dashed #e2e8f0; padding-bottom: 25px; margin-bottom: 25px; text-align: center; }
        .btn-guest { width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; background: #1e293b; color: white; padding: 16px; border-radius: 14px; text-decoration: none; font-weight: 700; transition: 0.2s ease; }
        .btn-guest:hover { background: #0f172a; transform: translateY(-1px); }
        
        .divider-text { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1.5px; margin-bottom: 20px; text-align: center; display: block; }
        
        label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: var(--text); }
        input { width: 100%; padding: 14px 16px; border-radius: 12px; border: 2px solid #f1f5f9; font-size: 0.95rem; margin-bottom: 18px; outline: none; transition: 0.2s; }
        input:focus { border-color: var(--primary); }
        
        .btn-login { width: 100%; background: var(--primary); color: white; padding: 14px; border-radius: 12px; border: none; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.2s; }
        .btn-login:hover { background: var(--primary-hover); }
        
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

    <div class="guest-section">
        <a href="dashboard.php" class="btn-guest">
            <i class="fas fa-user-secret"></i> Continue as Guest
        </a>
    </div>

    <span class="divider-text">Or Management Authentication</span>

    <?php echo $message; ?>
    <form method="POST" action="">
        <label>Personnel Email</label>
        <input type="email" name="email" placeholder="name@domain.com" required>
        
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
        
        <button type="submit" class="btn-login">Log In as Personnel</button>
    </form>

    <div class="footer-links">
        Need personnel access? &nbsp<a href="register.php">Register Shop Account</a>
    </div>
</div>

</body>
</html>