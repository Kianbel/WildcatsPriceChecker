<?php
session_start();
require_once 'includes/connect.php';

// 1. Guard Clause: Must be logged in and must be PERSONNEL
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PERSONNEL') {
    header("Location: login.php");
    exit();
}

// 2. Prepare variables for the Shared Sidebar
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$current_page = 'manage_shop'; // Keep 'My Shop' highlighted during creation

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_name = mysqli_real_escape_string($conn, $_POST['shop_name']);
    
    // Find the empid for this logged-in user
    $getEmp = mysqli_query($conn, "SELECT empid FROM tblpersonnel WHERE accid = '$user_id'");
    $empData = mysqli_fetch_assoc($getEmp);

    if ($empData) {
        $empid = $empData['empid'];

        // Insert the shop
        $sql = "INSERT INTO tblshop (sname, empid) VALUES ('$shop_name', '$empid')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: manage_shop.php");
            exit();
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Error: User is not registered as Personnel in the system.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Shop - WildCats</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #d32f2f;
            --primary-dark: #4a0000;
            --bg-main: #f8fafc;
            --sidebar-bg: #1e293b; 
            --sidebar-text: #f1f5f9;
            --sidebar-muted: #94a3b8;
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --sidebar-accent: #fca5a5;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-main); 
            color: var(--text-main);
            line-height: 1.5;
        }

        .app-container { display: flex; min-height: 100vh; width: 100%; }

        /* --- SHARED SIDEBAR STYLES (Exact Match) --- */
        .sidebar {
            width: 280px; background: var(--sidebar-bg); padding: 2.5rem 1.5rem;
            position: sticky; top: 0; height: 100vh; display: flex;
            flex-direction: column; color: var(--sidebar-text); z-index: 100;
        }
        .sidebar-inner { display: flex; flex-direction: column; height: 100%; }
        .avatar-container { margin-bottom: 1.5rem; }
        .user-avatar { width: 64px; height: 64px; border-radius: 18px; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.1); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); }
        .user-meta .u-name { display: block; font-weight: 700; font-size: 1.1rem; color: var(--sidebar-text); letter-spacing: -0.5px; }
        .user-meta .u-role { display: block; font-size: 0.75rem; font-weight: 800; color: var(--sidebar-accent); text-transform: uppercase; letter-spacing: 1.2px; margin-top: 2px; }
        .side-nav { margin-top: 2.5rem; flex-grow: 1; }
        .nav-group label { display: block; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.8px; color: var(--sidebar-muted); margin: 2rem 0 0.75rem 1rem; }
        .nav-item { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--sidebar-muted); padding: 12px 16px; border-radius: 12px; font-size: 0.95rem; font-weight: 500; transition: var(--transition); margin-bottom: 6px; }
        .nav-item:hover { background: var(--sidebar-hover); color: var(--sidebar-text); transform: translateX(4px); }
        .nav-item.active { background: var(--primary); color: white; font-weight: 700; box-shadow: 0 4px 15px rgba(211, 47, 47, 0.4); }
        .sidebar-footer { margin-top: auto; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .logout-btn { text-decoration: none; color: var(--sidebar-text); font-weight: 700; display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 12px; transition: var(--transition); }

        /* --- MAIN CONTENT --- */
        .main-content { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 2rem;
        }

        .setup-card {
            background: white;
            padding: 3.5rem;
            border-radius: 32px;
            box-shadow: var(--card-shadow);
            width: 100%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(241, 245, 249, 0.8);
        }

        .icon-box {
            width: 80px; height: 80px;
            background: rgba(211, 47, 47, 0.1);
            color: var(--primary);
            border-radius: 22px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; margin: 0 auto 1.5rem;
        }

        .setup-card h2 { font-weight: 800; letter-spacing: -1.5px; margin-bottom: 0.5rem; font-size: 1.75rem; }
        .setup-card p { color: var(--text-muted); margin-bottom: 2.5rem; font-size: 1rem; }

        .form-group { text-align: left; margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; color: #1e293b; }
        .form-group input { 
            width: 100%; padding: 16px; border-radius: 14px; 
            border: 2px solid #f1f5f9; font-family: inherit; font-size: 1rem; 
            transition: var(--transition);
        }
        .form-group input:focus { border-color: var(--primary); outline: none; background: white; }

        .btn-submit {
            width: 100%; padding: 18px; background: var(--primary); color: white;
            border: none; border-radius: 14px; font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: var(--transition);
        }
        .btn-submit:hover { background: var(--primary-dark); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(211, 47, 47, 0.2); }

        .alert-error { background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 600; }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 2rem 1rem; }
            .user-meta, .nav-group label, .nav-item span, .logout-btn span { display: none; }
        }
    </style>
</head>
<body>

<div class="app-container">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="setup-card">
            <div class="icon-box">
                <i class="fas fa-rocket"></i>
            </div>
            <h2>Launch Your Shop</h2>
            <p>Welcome to the team! Register your canteen stall name to start managing your prices.</p>

            <?php if ($error): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Shop Name</label>
                    <input type="text" name="shop_name" placeholder="e.g. Wildcats Burger Stand" required autofocus>
                </div>
                
                <button type="submit" class="btn-submit">Register & Initialize</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>