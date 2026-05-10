<?php
session_start();
require_once 'includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// 1. Set the current page (Fixed typo from 'dashbaord' to 'dashboard')
$current_page = 'dashboard';

// Initialize the shop link variable for the Hero/Grid logic
$my_shop_link = "create_shop.php"; 

$all_shops_query = mysqli_query($conn, "SELECT * FROM tblshop");

// If the user is PERSONNEL, check if they already have an empid and an associated shop
if ($user_role === 'PERSONNEL') {
    $emp_query = mysqli_query($conn, "SELECT empid FROM tblpersonnel WHERE accid = '$user_id'");
    $emp_data = mysqli_fetch_assoc($emp_query);

    if ($emp_data) {
        $empid = $emp_data['empid'];
        $shop_check = mysqli_query($conn, "SELECT sid FROM tblshop WHERE empid = '$empid' LIMIT 1");
        
        if (mysqli_num_rows($shop_check) > 0) {
            $my_shop_link = "manage_shop.php";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildCats - NextGen Dashboard</title>
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

        a {text-decoration: none;}

        .app-container { display: flex; min-height: 100vh; width: 100%; }

        /* --- SHARED SIDEBAR STYLES --- */
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
        .logout-btn:hover { background: rgba(255, 255, 255, 0.05); color: var(--sidebar-accent); }

        /* --- MAIN CONTENT --- */
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .hero { position: relative; padding: 7rem 5%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; overflow: hidden; }
        .hero::after { content: ''; position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%; }
        .welcome-heading { font-size: clamp(2.5rem, 5vw, 5rem); font-weight: 800; margin-bottom: 2rem; letter-spacing: -2px; position: relative; text-align: center; z-index: 1; }
        .search-bar-container { position: relative; z-index: 1; }
        .search-icon { position: absolute; left: 22px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem; }
        .search-bar-container input { width: 100%; padding: 20px 24px 20px 60px; border-radius: 18px; border: none; font-size: 1.05rem; outline: none; background: white; box-shadow: 0 20px 30px rgba(0,0,0,0.15); transition: var(--transition); }
        .search-bar-container input:focus { box-shadow: 0 25px 40px rgba(0,0,0,0.2); transform: scale(1.01); }

        .content-body { padding: 4rem 5%; max-width: 1600px; width: 100%; margin: 0 auto; }
        .section-title { margin-bottom: 3rem; }
        .section-title h2 { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.5px; }
        .section-title p { color: var(--text-muted); font-size: 1.1rem; }

        .shop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); gap: 3rem; }
        .modern-card { background: white; border-radius: 28px; overflow: hidden; box-shadow: var(--card-shadow); transition: var(--transition); border: 1px solid rgba(241, 245, 249, 0.8); display: flex; flex-direction: column; }
        .modern-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.12); }
        .card-image { height: 240px; background-size: cover; background-position: center; position: relative; }
        .card-tag { position: absolute; top: 20px; left: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(4px); padding: 6px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .card-content { padding: 2.25rem; flex-grow: 1; }
        .card-content h3 { font-size: 1.5rem; margin-bottom: 0.85rem; font-weight: 700; color: var(--text-main); }
        .card-content p { color: var(--text-muted); font-size: 1rem; line-height: 1.7; margin-bottom: 1.75rem; }
        .card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1.75rem; border-top: 1px solid #f1f5f9; }
        .card-footer span { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        .card-footer i { color: var(--primary); font-size: 1.2rem; transition: var(--transition); }
        .modern-card:hover .card-footer i { transform: translateX(8px); }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 2rem 1rem; }
            .user-meta, .nav-group label, .nav-item span, .logout-btn span { display: none; }
            .nav-item { justify-content: center; padding: 15px; }
            .shop-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="app-container">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="hero">
            <div class="hero-bg"></div>
            <div class="hero-body">
                <h1 class="welcome-heading">Wildcats Price Checker</h1>
                <div class="search-bar-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="What are you looking for today?">
                </div>
            </div>
        </header>

        <section class="content-body">
            <div class="section-title">
                <h2>Canteen Shops</h2>
                <p>Real-time price tracking for CIT-U campus.</p>
            </div>

            <div class="shop-grid">
    <?php 
    if (mysqli_num_rows($all_shops_query) > 0) {
        while ($shop = mysqli_fetch_assoc($all_shops_query)) { 
            ?>
            
            <a href="view_shop.php?shop_id=<?php echo $shop['sid']; ?>" class="card-link">
                <div class="modern-card">
                    <div class="card-image" style="background-image: url('https://www.clipartmax.com/png/full/2-21084_store-clipart-shop-building-clipart.png');">
                        <div class="card-tag">Shop</div>
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($shop['sname']); ?></h3>
                        <p><?php echo htmlspecialchars($shop['shop_description'] ?? 'No description available.'); ?></p>
                        <div class="card-footer">
                            <span>View Products</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>

        <?php 
        } 
    } else {
        echo "<p>No shops found in the campus yet.</p>";
    }
    ?>
</div>
        </section>
    </main>
</div>

</body>
</html>