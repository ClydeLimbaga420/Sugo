<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$worker_id = $_SESSION['user_id'];

$sql = "SELECT * FROM reports WHERE assigned_to = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt-> get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker | SUGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary: #2196f3;
            --secondary: #00bcd4;
            --success: #4caf50;
            --danger: #f44336;
            --bg-gradient: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            overflow-x: hidden;
        }


       
        .container {
            max-width: 1000px;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            animation: fadeInUp 0.6s ease-out;
            position: relative;
        }

        .brand-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f2f5;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo i {
            font-size: 32px;
            color: var(--primary);
            filter: drop-shadow(0 4px 6px rgba(33, 150, 243, 0.3));
        }

        .brand-logo h2 {
            margin: 0;
            font-weight: 600;
            letter-spacing: -0.5px;
            color: #333;
        }

        .logout-btn {
            background: #fff;
            color: var(--danger);
            border: 2px solid var(--danger);
            padding: 8px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 15px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        th {
            padding: 15px;
            text-align: left;
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        tbody tr {
            background: #fcfcfd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            animation: slideInLeft 0.5s ease backwards;
        }

        <?php for( $i = 1; $i <= 10; $i++ ): ?> 
            tbody tr:nth-child(<?= $i ?>) {
                animation-delay: <?= $i * 0.1 ?>s;
            }
        <?php endfor; ?>

        tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.86);
            background: #fff;
        }

        td {
            padding: 18px 15px;
            color: #444;
            font-size: 15px;
        }

        td:first-child {
            border-radius: 12px 0 0 12px;
            font-weight: 600;
            color: #aaa;
        }

        td:last-child {
            border-radius: 0 12px 12px 0;
        }

        .status {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .pending {
            background: #fff8e1;
            color: #ffa000;
        }

        .pending::before {
            background: #ffa000;
        }

        .assigned {
            background: #e3f2fd;
            color: #1976d2;
        }

        .assigned::before {
            background: #1976d2;
        }

        .resolved {
            background: #e8f5e9;
            color: #388e3c;
        }

        .resolved::before {
            background: #388e3c;
        }

        .btn-view {
            background: #f0f4f8;
            color: var(--primary);
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-view:hover {
            background: var(--primary);
            color: white;
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #bbb;
        }

        .empty i {
            font-size: 50px;
            margin-bottom: 15px;
            display: block;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .brand-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            th {
                display: none;
            }

            td {
                display: block;
                text-align: right;
                padding: 10px;
                border-bottom: 1px solid #f0f0f0;
            }

            td::before {
                content: attr(data-label);
                float: left;
                font-weight: 600;
                color: #888;
            }

            tbody tr {
                margin-bottom: 20px;
                display: block;
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="brand-header">
            <div class="brand-logo">
                <i class="fas fa-tools"></i>
                <div>
                    <h2>Hello Worker</h2>
                    <small style="color: #999;">Manage your assigned tasks</small>      
                </div>
            </div>
            <a href="../auth/login.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>  Logout
            </a>
        </div>

        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Issue Title</th>
                            <th>Current Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID">#<?= $row['id'] ?></td>
                                <td data-label="Title"><strong><?= htmlspecialchars($row['title']) ?></td>
                                <td data-label="Status">
                                    <span class="status <?= strtolower($row['status']) ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>

                                <td data-label="Action">
                                    <a class="btn-view" href="view_report.php?id=<?= $row['id'] ?>">
                                        <i class="fas fa-external-link-alt"></i>  Details
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty">
                        <i class="fas fa-clipboard-check"></i>
                        <p>No Assigned Reports Yet.</p>
                    </div>
                <?php endif; ?>
        </div>
    </div>

</body>
</html>