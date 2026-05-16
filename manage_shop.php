<?php
session_start();
require_once 'includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PERSONNEL') {
    header("Location: login.php");
    exit();
}

// Variables for Sidebar
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$current_page = 'manage_shop';

// Fetch Shop Data
$shopQuery = mysqli_query($conn, "SELECT * FROM tblshop WHERE empid = (SELECT empid FROM tblpersonnel WHERE accid = '$user_id') LIMIT 1");
$shop = mysqli_fetch_assoc($shopQuery);

if (!$shop) {
    header("Location: create_shop.php");
    exit();
}

$sid = $shop['sid'];
$sname = $shop['sname'];
$itemsQuery = mysqli_query($conn, "SELECT * FROM tblitem WHERE sid = '$sid' ORDER BY itemname ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($sname); ?> - WildCats</title>
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

        /* --- MAIN CONTENT & HERO --- */
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .hero { position: relative; padding: 5rem 5%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; overflow: hidden; }
        .welcome-heading { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; letter-spacing: -1px; z-index: 1; position: relative; }
        .header-actions { margin-top: 1.5rem; display: flex; gap: 12px; z-index: 1; position: relative; }
        .btn { padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: var(--transition); border: none; display: flex; align-items: center; gap: 8px; }
        .btn-white { background: white; color: var(--primary); }
        .btn-outline { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3); }
        .btn-outline-cancel { background: rgba(255,255,255,0.1); color: black; border: 1px solid rgba(255,255,255,0.3); }

        /* --- TABLE --- */
        .content-body { padding: 4rem 5%; }
        .items-card { background: white; border-radius: 28px; overflow: hidden; box-shadow: var(--card-shadow); border: 1px solid rgba(241, 245, 249, 0.8); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 20px 25px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); }
        td { padding: 20px 25px; border-top: 1px solid #f1f5f9; font-weight: 500; }
        .price-tag { font-weight: 800; color: var(--primary); font-size: 1.1rem; }
        .actions { display: flex; gap: 10px; }
        .action-btn { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; }
        .edit { background: #e0f2fe; color: #0369a1; }
        .delete { background: #fee2e2; color: #991b1b; }

        /* --- MODALS --- */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: white; width: 90%; max-width: 450px; border-radius: 30px; padding: 40px; animation: slideUp 0.4s ease; }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #f1f5f9; }
        .modal-footer { display: flex; gap: 12px; margin-top: 30px; justify-content: center; }
        
        @media (max-width: 1024px) { .sidebar { width: 80px; padding: 2rem 1rem; } .user-meta, .nav-group label, .nav-item span, .logout-btn span { display: none; } }
    </style>
</head>
<body>

<div class="app-container">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="hero">
            <h1 class="welcome-heading"><?php echo htmlspecialchars($sname); ?></h1>
            
            <?php if (!empty($shop['shop_description'])): ?>
                <p style="opacity: 0.9; margin-top: 0.5rem; font-weight: 500; max-width: 600px;">
                    <?php echo htmlspecialchars($shop['shop_description']); ?>
                </p>
            <?php endif; ?>

            <div class="header-actions">
                <button onclick="openShopEditModal('<?php echo addslashes(htmlspecialchars($sname)); ?>')" class="btn btn-outline">
                    <i class="fas fa-pen"></i> Rename Shop
                </button>
                <button onclick="openDescriptionModal('<?php echo addslashes(htmlspecialchars($shop['shop_description'] ?? '')); ?>')" class="btn btn-outline">
                    <i class="fas fa-align-left"></i> <?php echo empty($shop['shop_description']) ? 'Add Description' : 'Edit Description'; ?>
                </button>
                <button onclick="openModal()" class="btn btn-white"><i class="fas fa-plus"></i> Add New Item</button>
            </div>
        </header>

        <section class="content-body">
            <?php if (isset($_GET['msg'])): ?>
                <div id="statusAlert" style="padding: 15px 20px; background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; border-radius: 14px; margin-bottom: 20px; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                        <?php
                            switch ($_GET['msg']) {
                                case 'item_added': echo 'Item successfully created!'; break;
                                case 'item_updated': echo 'Item successfully updated!'; break;
                                case 'item_deleted': echo 'Item successfully deleted!'; break;
                                case 'shop_updated': echo 'Shop name updated successfully!'; break;
                                case 'description_updated': echo 'Shop description updated successfully!'; break;
                                default: echo 'Action processed successfully.';
                            }
                        ?>
                    </span>
                    <button onclick="document.getElementById('statusAlert').style.display='none'" style="background: none; border: none; color: #0f5132; cursor: pointer; font-size: 1.2rem;"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <div class="items-card">
                <table>
                    <thead>
                        <tr><th>Item Name</th><th>Current Price</th><th>Management</th></tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($itemsQuery) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($itemsQuery)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['itemname']); ?></strong></td>
                                    <td class="price-tag">₱<?php echo number_format($row['price'], 2); ?></td>
                                    <td class="actions">
                                        <button onclick="openEditModal(<?php echo $row['itemid']; ?>, '<?php echo addslashes(htmlspecialchars($row['itemname'])); ?>', <?php echo $row['price']; ?>)" class="action-btn edit"><i class="fas fa-edit"></i></button>
                                        
                                        <a href="process_delete_item.php?id=<?php echo $row['itemid']; ?>" 
                                        class="action-btn delete" 
                                        onclick="handleDeleteConfirm(event, this.href, '<?php echo addslashes(htmlspecialchars($row['itemname'])); ?>', '<?php echo number_format($row['price'], 2); ?>');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; padding: 50px; color: var(--text-muted);">No items yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <h3>New Item</h3>
        <form action="process_add_item.php" method="POST" id="addItemForm" 
              onsubmit="handleCustomConfirm(event, 'addItemForm', 'Add New Item', 'Are you sure you want to add <strong>' + htmlEntities(this.itemname.value) + '</strong> costing <strong>₱' + parseFloat(this.price.value).toFixed(2) + '</strong> to your shop?');">
            <div class="form-group"><label>Item Name</label><input type="text" name="itemname" placeholder="e.g. Pencil" required></div>
            <div class="form-group"><label>Price (₱)</label><input type="number" name="price" step="0.01" placeholder="e.g. 10.00" required></div>
            <input type="hidden" name="sid" value="<?php echo $sid; ?>">
            <div class="modal-footer"><button type="button" class="btn btn-outline-cancel" onclick="closeModal()">Cancel</button><button type="submit" class="btn btn-white" style="background:var(--primary); color:white">Save</button></div>
        </form>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <h3>Update Item</h3>
        <form action="process_edit_item.php" method="POST" id="editItemForm" 
              onsubmit="handleCustomConfirm(event, 'editItemForm', 'Update Item', 'Are you sure you want to update this item to <strong>' + htmlEntities(this.edit_itemname.value) + '</strong> with a price of <strong>₱' + parseFloat(this.edit_price.value).toFixed(2) + '</strong>?');">
            <input type="hidden" name="itemid" id="edit_itemid">
            <div class="form-group"><label>Item Name</label><input type="text" name="itemname" id="edit_itemname" required></div>
            <div class="form-group"><label>Price (₱)</label><input type="number" name="price" id="edit_price" step="0.01" required></div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-cancel" style="color: black" onclick="closeEditModal()">Cancel</button><button type="submit" class="btn btn-white" style="background:var(--primary); color:white">Update</button></div>
        </form>
    </div>
</div>

<div id="shopEditModal" class="modal-overlay">
    <div class="modal-content">
        <h3>Rename Shop</h3>
        <form action="process_edit_shop.php" method="POST" id="editShopForm" 
              onsubmit="handleCustomConfirm(event, 'editShopForm', 'Rename Shop', 'Are you sure you want to change your shop name to <strong>' + htmlEntities(this.sname.value) + '</strong>?');">
            <div class="form-group"><label>New Name</label><input type="text" name="sname" id="edit_sname" required></div>
            <input type="hidden" name="sid" value="<?php echo $sid; ?>">
            <div class="modal-footer"><button type="button" class="btn btn-outline-cancel" onclick="closeShopEditModal()">Cancel</button><button type="submit" class="btn btn-white" style="background:var(--primary); color:white">Confirm</button></div>
        </form>
    </div>
</div>

<div id="shopDescriptionModal" class="modal-overlay">
    <div class="modal-content">
        <h3>Shop Description</h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">Tell customers about your shop (e.g., location, hours, or specialties).</p>
        <form action="process_edit_shop_description.php" method="POST" id="shopDescForm" 
              onsubmit="handleCustomConfirm(event, 'shopDescForm', 'Update Description', 'Are you sure you want to update your shop description to:<br><br><blockquote style=\'background: #f1f5f9; padding: 15px; border-left: 4px solid var(--primary); border-radius: 12px; font-style: italic; text-align: left; max-height: 150px; overflow-y: auto; white-space: pre-wrap;\'>' + htmlEntities(document.getElementById('edit_shop_description').value) + '</blockquote>');">
            <div class="form-group">
                <label>Description</label>
                <textarea name="shop_description" id="edit_shop_description" style="width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #f1f5f9; font-family: inherit; resize: vertical;" rows="4" placeholder="Enter shop details..."></textarea>
            </div>
            <input type="hidden" name="sid" value="<?php echo $sid; ?>">
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-cancel" onclick="closeDescriptionModal()">Cancel</button>
                <button type="submit" class="btn btn-white" style="background:var(--primary); color:white">Save Description</button>
            </div>
        </form>
    </div>
</div>

<div id="customConfirmModal" class="modal-overlay" style="z-index: 2000;">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <div style="font-size: 3rem; color: var(--primary); margin-bottom: 15px;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h3 id="confirmTitle" style="margin-bottom: 10px;">Are you sure?</h3>
        <p id="confirmMessage" style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 25px;">Do you really want to proceed?</p>
        
        <div class="modal-footer" style="justify-content: center; gap: 15px;">
            <button type="button" class="btn btn-outline-cancel" id="confirmCancelBtn" style="min-width: 100px;">Cancel</button>
            <button type="button" class="btn btn-white" id="confirmProceedBtn" style="background: var(--primary); color: white; min-width: 100px;">Confirm</button>
        </div>
    </div>
</div>

<script>
    let targetFormId = null;
    let targetDeleteUrl = null;
    let isConfirmed = false;

    // Helper function to escape HTML strings safely before injecting into innerHTML
    function htmlEntities(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Intercepts form submissions to show custom confirmation modal
    function handleCustomConfirm(event, formId, title, messageHtml) {
        if (!isConfirmed) {
            event.preventDefault(); // Stop form from submitting immediately
            targetFormId = formId;
            targetDeleteUrl = null; 
            
            document.getElementById('confirmTitle').innerText = title;
            document.getElementById('confirmMessage').innerHTML = messageHtml;
            
            document.getElementById('customConfirmModal').style.display = 'flex';
        }
    }

    // Intercepts delete link clicks with dynamic Item details
    function handleDeleteConfirm(event, url, itemName, itemPrice) {
        event.preventDefault(); // Stop immediate redirection
        targetDeleteUrl = url;
        targetFormId = null; 
        
        document.getElementById('confirmTitle').innerText = 'Delete Item';
        document.getElementById('confirmMessage').innerHTML = 'Are you sure you want to permanently delete <strong>' + htmlEntities(itemName) + ' </strong> &nbsp(₱' + itemPrice + ')? &nbsp This action cannot be undone.';
        
        document.getElementById('customConfirmModal').style.display = 'flex';
    }

    // Close the custom confirmation modal
    function closeCustomConfirm() {
        document.getElementById('customConfirmModal').style.display = 'none';
        targetFormId = null;
        targetDeleteUrl = null;
        isConfirmed = false;
    }

    // Executed when user clicks "Confirm" in the custom modal
    document.getElementById('confirmProceedBtn').addEventListener('click', function() {
        isConfirmed = true;
        if (targetFormId) {
            document.getElementById(targetFormId).submit();
        } else if (targetDeleteUrl) {
            window.location.href = targetDeleteUrl;
        }
        closeCustomConfirm();
    });

    document.getElementById('confirmCancelBtn').addEventListener('click', closeCustomConfirm);

    // Reusable single Click outside handler
    window.onclick = function(e) {
        if(e.target.className === 'modal-overlay') { 
            if (e.target.id === 'customConfirmModal') {
                closeCustomConfirm();
            } else {
                closeModal(); 
                closeEditModal(); 
                closeShopEditModal(); 
                closeDescriptionModal(); 
            }
        }
    }

    function openModal() { document.getElementById('addModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('addModal').style.display = 'none'; }
    
    function openEditModal(id, name, price) {
        document.getElementById('edit_itemid').value = id;
        document.getElementById('edit_itemname').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
    
    function openShopEditModal(name) { 
        document.getElementById('edit_sname').value = name; 
        document.getElementById('shopEditModal').style.display = 'flex'; 
    }
    function closeShopEditModal() { document.getElementById('shopEditModal').style.display = 'none'; }

    function openDescriptionModal(desc) { 
        document.getElementById('edit_shop_description').value = desc; 
        document.getElementById('shopDescriptionModal').style.display = 'flex'; 
    }
    function closeDescriptionModal() { 
        document.getElementById('shopDescriptionModal').style.display = 'none'; 
    }

    // Auto-hide the success alert banner after 2 seconds
    window.addEventListener('DOMContentLoaded', (event) => {
        const alert = document.getElementById('statusAlert');
        if (alert) {
            setTimeout(() => { alert.style.display = 'none'; }, 4000);
        }
    });
</script>
</body>
</html>