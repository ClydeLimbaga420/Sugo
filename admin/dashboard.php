<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$admin_name = $_SESSION['name'] ?? $_SESSION['username'] ?? 'Administrator';
$sql = "SELECT r.*, u.name AS user_name FROM reports r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Center | SUGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --admin-primary: #673ab7;
            --admin-secondary: #9c27b0;
            --success: #4caf50;
            --danger: #f44336;
            --warning: #ff9800;
            --bg-gradient: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            color: #333;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .container {
            max-width: 1100px;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 20px;
        }

        .welcome-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-icon {
            background: var(--admin-primary);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 10px 20px rgba(103, 58, 183, 0.3);
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .header p {
            margin: 0;
            color: #888;
            font-size: 14px;
        }

        .logout-btn {
            background: #fff;
            color: var(--danger);
            border: 2px solid var(--danger);
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        th {
            background: transparent;
            color: #aaa;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1.5px;
            padding: 10px 20px;
            font-weight: 600;
        }

        tbody tr {
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            margin-bottom: 10px;
        }

        <?php for($i=1; $i<=15; $i++): ?>
            tbody tr:nth-child(<?= $i ?>) {
                animation-delay: <?= $i * 0.05 ?>s;
            }
        <?php endfor; ?>

        tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.01);
        
        }

        td {
            padding: 20px;
            font-size: 14px;
            color: #444;
            border: none;
        }

        td:first-child {
            
            border-radius: 15px 0 0 15px;
        
        }

        td:last-child {
            
            border-radius: 0 15px 15px 0;
        }

        .status {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pending {
            background: #fff8e1;
            color: var(--warning);
        }

        .assigned {
            background: #e8f5e9;
            color: var(--success);
        }

        .resolved {
            background: #e1f5fe;
            color: #03a9f4;
        }

        .action-group {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 8px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view {
            background: #f0f2f5;
            color: #555;
        }
        
        .btn-view:hover {
            background: #e0e4e8;
            color: #333;
        }

        .btn-assign {
            background: #ede7f6;
            color: var(--admin-primary)
        }

        .btn-assign:hover {
            background: var(--admin-primary);
            color: white;
        }

        .empty {
            text-align: center;
            padding: 50px;
            color: #ccc;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
            }

            th {
                display: none;
            }

            td {
                display: block;
                text-align: right;
                padding: 10px 20px;
                border: none;
            }

            td::before {
                content: attr(data-label);
                float: left;
                font-weight: 600;
                color: #aaa;
            }

            tbody tr {
                margin-bottom: 20px;
                display: block;
                border-radius: 15px;
                border: 1px solid #eee;
            }
        }
    </style>

</head>
<body>
    
    <div class="container">
        <div class="header">
            <div class="welcome-group">
                <div class="admin-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back, <strong><?= htmlspecialchars($admin_name) ?></strong></p>
                </div>
            </div>
            <a href="../auth/login.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Respondent</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()):
                            $statusClass = strtolower($row['status']);
                        ?>
                        <tr>
                            <td data-label="ID">#<?= htmlspecialchars($row['id']) ?></td>
                            <td data-label="Respondent">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-user-circle" style="color: #ddd; font-size: 18px;"></i>
                                    <?= htmlspecialchars($row['user_name']) ?>
                                </div>
                            </td>
                            <td data-label="Title"><?= htmlspecialchars($row['title']) ?></td>
                            <td data-label="Status"> 
                                <span class="status <?= $statusClass ?>">
                                    <i class="fas fa-circle" style="font-size: 6px;"></i>
                                    <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                </span>
                            </td>
                            <td data-label="Manage">
                                <div class="action-group">
                                    <a href="report_view.php?id=<?= $row['id'] ?>" class="btn-action btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="assign_worker.php?id=<?= $row['id'] ?>" class="btn-action btn-assign">
                                        <i class="fas fa-user-plus"></i> Assign
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty">
                                <i class="fas fa-inbox" style="font-size: 40px; display:block; margin-bottom: 10px;"></i>
                                No reports found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>