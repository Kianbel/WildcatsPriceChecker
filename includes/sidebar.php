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
                <label>Menu</label>
                <a href="dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-house"></i> Dashboard
                </a>
                
                <?php if ($user_role === 'PERSONNEL'): ?>
                    <a href="manage_shop.php" class="nav-item <?php echo ($current_page == 'manage_shop') ? 'active' : ''; ?>">
                        <i class="fas fa-store"></i> My Shop
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</aside>