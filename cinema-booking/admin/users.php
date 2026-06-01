<?php
require_once '../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../index.php?error=Access denied');
}
$pageTitle = "Manage Users";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .admin-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        .admin-main {
            padding: 20px;
            background: #f8f9fa;
        }
         .admin-nav ul {
            list-style: none;
            padding: 0;
        }
        
        .admin-nav li {
            margin: 5px 0;
        }
        
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            transition: all 0.3s;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(233, 69, 96, 0.1);
            border-left: 4px solid #e94560;
            color: #e94560;
        }
        .admin-nav i {
            width: 20px;
            text-align: center;
        }
        .admin-sidebar {
            background: #1a1a2e;
            color: white;
            padding: 20px 0;
        }
        .admin-logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-logo h2 {
            color: #e94560;
            font-size: 1.5rem;
        }
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-header h1 {
            color: #1a1a2e;
            margin: 0;
        }
        .add-btn {
            background: #e94560;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .users-table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-responsive {
            overflow-x: auto;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        .users-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #1a1a2e;
            border-bottom: 2px solid #e94560;
        }
        .users-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .users-table tr:hover {
            background: #f9f9f9;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e94560;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .role-admin {
            background: #e94560;
            color: white;
        }
        .role-user {
            background: #0f3460;
            color: white;
        }
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .btn-edit {
            background: #0f3460;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-delete {
            background: #e94560;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-reset {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><i class="fas fa-film"></i> CineBook Admin</h2>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li>
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="movies.php">
                            <i class="fas fa-film"></i> Movies
                        </a>
                    </li>
                    <li>
                        <a href="add_movie.php">
                            <i class="fas fa-plus-circle"></i> Add Movie
                        </a>
                    </li>
                    <li>
                        <a href="bookings.php">
                            <i class="fas fa-ticket-alt"></i> Bookings
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="active">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li>
                        <a href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="page-header">
                <h1><i class="fas fa-users"></i> Manage Users</h1>
            </div>
            <div class="users-table-container">
                <div class="table-responsive">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Bookings</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php
                            $sql = "SELECT u.*, COUNT(b.id) as booking_count 
                                   FROM users u 
                                   LEFT JOIN bookings b ON u.id = b.user_id 
                                   GROUP BY u.id 
                                   ORDER BY u.created_at DESC";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0):
                                while($user = $result->fetch_assoc()):
                                    $initials = strtoupper(substr($user['username'], 0, 2));
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div class="user-avatar">
                                            <?php echo $initials; ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong><br>
                                            <small style="color: #666;">ID: <?php echo $user['id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td><?php echo $user['booking_count']; ?> bookings</td>
                                <td>
                                    <span style="color: #28a745; font-weight: 600;">Active</span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        
                                        <button class="btn-reset" onclick="resetPassword(<?php echo $user['id']; ?>)">
                                            <i class="fas fa-key"></i> Reset
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button class="btn-delete" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-users" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                                    <h3 style="color: #666;">No Users Found</h3>
                                    <p>No users have registered yet.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/script.js"></script>
    <script>
        function editUser(userId) {
            alert('Edit user ' + userId + '\n\nIn a real application, this would open an edit form.');
        }
        function resetPassword(userId) {
            if (confirm('Reset password for user ID ' + userId + '?\n\nA temporary password will be generated and sent to the user.')) {
                const btn = event.target.closest('.btn-reset');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-check"></i> Sent';
                    btn.style.background = '#6c757d';
                    btn.disabled = true;
                    
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.background = '';
                        btn.disabled = false;
                    }, 3000);
                    
                    alert('Password reset email sent successfully!');
                }, 1500);
            }
        }
        
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?\n\nThis action will also delete all their bookings and cannot be undone.')) {
                const btn = event.target.closest('.btn-delete');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                
                setTimeout(() => {
                    const row = btn.closest('tr');
                    row.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        row.remove();
                        
                        if (document.querySelectorAll('#usersTableBody tr').length === 0) {
                            const tbody = document.getElementById('usersTableBody');
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-users" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                                        <h3 style="color: #666;">No Users Found</h3>
                                        <p>No users have registered yet.</p>
                                    </td>
                                </tr>
                            `;
                        }
                    }, 500);
                    
                    alert('User deleted successfully!');
                }, 1500);
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const roleCells = document.querySelectorAll('.users-table td:nth-child(3)');
            roleCells.forEach(cell => {
                const badge = cell.querySelector('.role-badge');
                const userId = cell.closest('tr').querySelector('td small').textContent.split(': ')[1];
                
                if (badge.textContent === 'User') {
                    const promoteBtn = document.createElement('button');
                    promoteBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
                    promoteBtn.title = 'Promote to Admin';
                    promoteBtn.style.cssText = `
                        background: #e94560;
                        color: white;
                        border: none;
                        width: 24px;
                        height: 24px;
                        border-radius: 50%;
                        cursor: pointer;
                        margin-left: 10px;
                        font-size: 10px;
                    `;
                    promoteBtn.onclick = function(e) {
                        e.stopPropagation();
                        promoteUser(userId, cell);
                    };
                    cell.appendChild(promoteBtn);
                } else if (badge.textContent === 'Admin') {
                    const demoteBtn = document.createElement('button');
                    demoteBtn.innerHTML = '<i class="fas fa-arrow-down"></i>';
                    demoteBtn.title = 'Demote to User';
                    demoteBtn.style.cssText = `
                        background: #0f3460;
                        color: white;
                        border: none;
                        width: 24px;
                        height: 24px;
                        border-radius: 50%;
                        cursor: pointer;
                        margin-left: 10px;
                        font-size: 10px;
                    `;
                    demoteBtn.onclick = function(e) {
                        e.stopPropagation();
                        demoteUser(userId, cell);
                    };
                    cell.appendChild(demoteBtn);
                }
            });
        });
        
        function promoteUser(userId, cell) {
            if (confirm('Promote this user to Admin?\n\nAdmins will have access to the admin dashboard.')) {
                const badge = cell.querySelector('.role-badge');
                badge.textContent = 'Admin';
                badge.className = 'role-badge role-admin';
                
                const promoteBtn = cell.querySelector('button');
                promoteBtn.remove();
                
                const demoteBtn = document.createElement('button');
                demoteBtn.innerHTML = '<i class="fas fa-arrow-down"></i>';
                demoteBtn.title = 'Demote to User';
                demoteBtn.style.cssText = `
                    background: #0f3460;
                    color: white;
                    border: none;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    cursor: pointer;
                    margin-left: 10px;
                    font-size: 10px;
                `;
                demoteBtn.onclick = function(e) {
                    e.stopPropagation();
                    demoteUser(userId, cell);
                };
                cell.appendChild(demoteBtn);
                
                alert('User promoted to Admin successfully!');
            }
        }
        
        function demoteUser(userId, cell) {
            if (confirm('Demote this admin to User?\n\nThey will lose access to the admin dashboard.')) {
                const badge = cell.querySelector('.role-badge');
                badge.textContent = 'User';
                badge.className = 'role-badge role-user';
                const demoteBtn = cell.querySelector('button');
                demoteBtn.remove();
                
                const promoteBtn = document.createElement('button');
                promoteBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
                promoteBtn.title = 'Promote to Admin';
                promoteBtn.style.cssText = `
                    background: #e94560;
                    color: white;
                    border: none;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    cursor: pointer;
                    margin-left: 10px;
                    font-size: 10px;
                `;
                promoteBtn.onclick = function(e) {
                    e.stopPropagation();
                    promoteUser(userId, cell);
                };
                cell.appendChild(promoteBtn);
                
                alert('Admin demoted to User successfully!');
            }
        }
    </script>
</body>
</html>