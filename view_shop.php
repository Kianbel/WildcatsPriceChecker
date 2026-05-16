<?php
session_start();
require_once 'includes/connect.php';

// 1. Security Check: Ensure shop_id exists
if (!isset($_GET['shop_id'])) {
    header("Location: dashboard.php");
    exit();
}

// 2. Sidebar Identity Variables (Supporting Open Guest Access)
// These match the updated fallback logic used across dashboard.php and sidebar.php
$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['user_name'] ?? 'Guest Explorer';
$user_role = $_SESSION['user_role'] ?? 'GUEST'; 
$current_page = 'dashboard'; // Keeps Dashboard highlight active in sidebar

$shop_id = mysqli_real_escape_string($conn, $_GET['shop_id']);

// 3. Fetch Shop Details
$shop_query = mysqli_query($conn, "SELECT * FROM tblshop WHERE sid = '$shop_id'");
$shop = mysqli_fetch_assoc($shop_query);

if (!$shop) {
    header("Location: dashboard.php");
    exit();
}

$sname = $shop['sname']; 
$shop_desc = $shop['shop_description'] ?? 'No description available.';

// 4. Fetch Items
$items_query = mysqli_query($conn, "SELECT * FROM tblitem WHERE sid = '$shop_id' ORDER BY itemname ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($sname); ?> - WildCats</title>
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

        /* --- SHARED SIDEBAR STYLES FROM DASHBOARD --- */
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
        
        /* --- CONTENT STYLES --- */
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .hero { position: relative; padding: 5rem 5%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; overflow: hidden; }
        .welcome-heading { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; letter-spacing: -1px; z-index: 1; position: relative; }
        .back-link { margin-top: 1rem; display: inline-flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.8); text-decoration: none; font-weight: 600; font-size: 0.9rem; z-index: 1; position: relative; transition: var(--transition); margin-bottom: 0.5rem; }
        .back-link:hover { color: white; transform: translateX(-4px); }

        .content-body { padding: 4rem 5%; }
        .items-card { background: white; border-radius: 28px; overflow: hidden; box-shadow: var(--card-shadow); border: 1px solid rgba(241, 245, 249, 0.8); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 20px 25px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); }
        td { padding: 20px 25px; border-top: 1px solid #f1f5f9; font-weight: 500; }
        .price-tag { font-weight: 800; color: var(--primary); font-size: 1.1rem; }
        
        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 2rem 1rem; }
            .user-meta, .nav-group label, .nav-item span, .logout-btn span{ display: none; }
        }
    </style>
</head>
<body>

<div class="app-container">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="hero">
            <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Shops</a>
            <h1 class="welcome-heading"><?php echo htmlspecialchars($sname); ?></h1>
            <p style="opacity: 0.9; margin-top: 0.25rem; font-weight: 500; max-width: 600px;">
                <?php echo htmlspecialchars($shop_desc); ?>
            </p>
        </header>

        <section class="content-body">
            <div class="items-card">
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($items_query) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($items_query)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['itemname']); ?></strong></td>
                                    <td class="price-tag">₱<?php echo number_format($row['price'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 50px; color: var(--text-muted);">
                                    No items listed for this shop yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

</body>
</html>