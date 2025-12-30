<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM reports WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUGO | My Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2196f3;
            --success-color: #4caf50;
            --pending-color: #ff9800;
            --danger-color: #f44336;
            --bg-gradient: linear-gradient(-45deg, #00bcd4, #2196f3, #3f51b5, #00bcd4);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            max-width: 1000px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .title-area h2 {
            margin: 0;
            color: #333;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background-color: var(--primary-color);
            color:white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        }

        .btn:hover {
            background-color: #1976d2;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(33, 150, 243, 0.4);
        }

        .btn-home {
            background-color: #607d8b;
        }

        btn-home:hover {
            background-color: #455a64;
        }

        .table-wrapper {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #eee;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f8f9fa;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #444;
            font-size: 14px;
        }

        tr {
            transition: 0.2s;
        }

        tr:hover {
            background-color: #f1f8ff;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status.assigned {
            background: #cfe2ff;
            color: #084298;
            border: 1px solid #b6d4fe;
        }

        .status.resolved {
            background: #d1e7dd;
            color: #084298;
            border: 1px solid #badbcc;
        }

        .btn-view {
            padding: 6px 15px;
            font-size: 12px;
            background: #f0f2f5;
            color: #333;
            border: 1px solid #ddd;
            box-shadow: none;
        }

        .btn-view:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #888;
        }

        .empty-state i {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 15px;
        }

        @media (max-width: 600px) {
            .header-flex {
                flex-direction: column;
                align-items: stretch;
            }
            .btn {
                justify-content: center;
            }
        }

    </style>
</head>
<body>
    
    <div class="container">
        <div class="header-flex">
            <div class="title-area">
                <h2><i class="fas fa-folder-open"></i> My Reports</h2>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="home.php" class="btn btn-home"><i class="fas fa-home"></i> Home</a>
                <a href="report_create.php" class="btn"><i class="fas fa-plus"></i> New Report</a>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Issue Title</th>
                        <th>Status</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()):
                        $statusClass = strtolower($row['status']);
                    ?>
                    <tr>
                        <td><span style="color: #999; font-weight: 600;">#<?= $row['id'] ?></span></td>
                        <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                        <td>
                            <span class="status <?= $statusClass ?>">
                                <?= ucfirst(htmlspecialchars($row['status'])) ?>
                            </span>
                        </td>

                        <td>
                            <?= date('M d, Y', strtotime($row['created_at'])) ?>
                        </td>

                        <td>
                            <a class="btn btn-view" href="view_report_user.php?id=<?= $row['id'] ?>">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard"></i>
                                    <p>You have no reports yet.</p>
                                    <a href="report_create.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Click here to start.</a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>