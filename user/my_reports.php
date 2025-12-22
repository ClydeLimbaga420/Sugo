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
    <title>My Reports</title>

    <style>
    
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8; 
        }
        .container {
            max-width: 1100px; 
            margin: 40px auto; 
            background: #fff; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.08); 
        }

        h2 {
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px; 
            background-color: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #1976d2;
        }
        .table-wrapper {
            overflow-x: auto; 
            margin-top: 20px; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px; 
        }

        th, td {
            padding: 12px; 
            border-bottom: 1px solid #ddd; 
            text-align: left;
        }

        th {
            background-color: #f0f2f5; 
            font-weight: bold;
        }

        tr:hover {
            background-color: #fafafa; 
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }

        .status.pending { 
            background: #fff3cd; 
            color: #856404; 
        }   
        .status.approved { 
            background: #d4edda; 
            color: #155724; 
        }  
        .status.rejected { 
            background: #f8d7da; 
            color: #721c24; 
        }
        
        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    

    <h2>My Reports</h2>

    <a class="btn" href="create_report.php">+ Submit New Report</a>
    

    <div class="table-wrapper">
        
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Action</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php
                    
                        $statusClass = strtolower($row['status']); 
                        
                    ?>

                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>

                        <td>
                            
                            <span class="status <?= $statusClass ?>">
                                <?= ucfirst(htmlspecialchars($row['status'])) ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($row['created_at']) ?></td>

                        <td>
                            <a class="btn" href="view_report_user.php?id=<?= $row['id'] ?>">
                                View
                            </a>
                        </td>
                    </tr>

                <?php endwhile; ?>
            <?php else: ?>

                
                <tr>
                    <td colspan="5" class="empty">
                        No reports submitted yet.
                    </td>
                </tr>

            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>
