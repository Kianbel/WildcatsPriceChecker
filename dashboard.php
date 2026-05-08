<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildCats - NextGen Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-inner">
            <div class="user-profile">
                <div class="avatar-container">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=800000&color=fff" alt="Avatar" class="user-avatar">
                </div>
                <div class="user-meta">
                    <span class="u-name"><?php echo htmlspecialchars($user_name); ?></span>
                    <span class="u-role"><?php echo htmlspecialchars($user_role); ?></span>
                </div>
            </div>

            <nav class="side-nav">
                <div class="nav-group">
                    <label>Workspace</label>
                    <a href="#" class="nav-item active"><i class="fas fa-house"></i> Dashboard</a>
                    <a href="#" class="nav-item"><i class="fas fa-store"></i> View Shop</a>
                </div>

                <?php if ($user_role == "PERSONNEL"): ?>
                <div class="nav-group">
                    <label>Management</label>
                    <a href="#" class="nav-item"><i class="fas fa-plus-square"></i> Add Item</a>
                    <a href="#" class="nav-item"><i class="fas fa-trash-can"></i> Delete Item</a>
                    <a href="#" class="nav-item"><i class="fas fa-pen-to-square"></i> Update Item</a>
                </div>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="hero">
            <div class="hero-bg"></div>
            <div class="hero-body">
                <h1 class="welcome-heading">Welcome back.</h1>
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
                <div class="modern-card">
                    <div class="card-image" style="background-image: url('https://scontent.fceb6-3.fna.fbcdn.net/v/t39.30808-6/484808875_1132500665342598_8952838221900438634_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=2a1932&_nc_ohc=jeQW3t_us5QQ7kNvwGOduUC&_nc_oc=AdqweDTlWZErApcj9yomK3Tl9Mg_pbgp0MLuv-_Q9fAfmpnsRua_aBIr1PiAyTGqXw8&_nc_zt=23&_nc_ht=scontent.fceb6-3.fna&_nc_gid=eEz8Jgj6cWt-DPhv1BdmFA&_nc_ss=7b289&oh=00_Af4opJtm9zqKlgixDVRhz6eeoM1eYckm53fTzUChxKUXkQ&oe=6A032491');">
                        <div class="card-tag">Supplies</div>
                    </div>
                    <div class="card-content">
                        <h3>CIT-U School Supplies</h3>
                        <p>Essential stationery, papers, and campus gear.</p>
                        <div class="card-footer">
                            <span>24 Items Available</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>

                <div class="modern-card">
                    <div class="card-image" style="background-image: url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=800');">
                        <div class="card-tag">DRINKS</div>
                    </div>
                    <div class="card-content">
                        <h3>Coffito</h3>
                        <p>Premium artisanal coffee and beverages.</p>
                        <div class="card-footer">
                            <span>12 Items Available</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>