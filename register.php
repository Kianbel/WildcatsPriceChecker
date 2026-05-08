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
        // 1. Check if email exists in tbluser[cite: 1, 5]
        $checkEmail = $conn->prepare("SELECT email FROM tbluser WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert error'>Email already registered.</div>";
        } else {
            // Start a transaction to ensure both tables are updated or none at all
            $conn->begin_transaction();

            try {
                // 2. Insert into tbluser[cite: 1, 5]
                $stmt = $conn->prepare("INSERT INTO tbluser (fname, lname, email, password, usertype) VALUES (?, ?, ?, ?, ?)");
                // Map "Student" to "S" and "Personnel" to "P" to match usertype varchar(1)
                $userTypeCode = ($type == "Student") ? "S" : "P";
                $stmt->bind_param("sssss", $fname, $lname, $email, $pass, $userTypeCode);
                $stmt->execute();

                // 3. Get the generated accid
                $last_id = $conn->insert_id;

                // 4. Insert into specific subtype table
                if ($type == "Student") {
                    $stmtSub = $conn->prepare("INSERT INTO tblstudent (accid) VALUES (?)");
                } else {
                    $stmtSub = $conn->prepare("INSERT INTO tblpersonnel (accid) VALUES (?)");
                }
                
                $stmtSub->bind_param("i", $last_id);
                $stmtSub->execute();

                // Commit the transaction
                $conn->commit();
                
                $message = "<div class='alert success'>Account created! <a href='login.php'>Sign In</a></div>";
                header("Location: login.php");
            } catch (Exception $e) {
                // Rollback on error
                $conn->rollback();
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