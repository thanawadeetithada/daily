<?php
session_start();
include 'db.php';

// Handle add
if (isset($_POST['add_entry'])) {
    $date = $_POST['date'];
    $description = $_POST['description'];
    $income = $_POST['income'] ?: 0;
    $expense = $_POST['expense'] ?: 0;
    $stmt = $conn->prepare("INSERT INTO income_expense (date, description, income, expense) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdd", $date, $description, $income, $expense);
    $stmt->execute();
    header("Location: income_expenses.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM income_expense WHERE id=$id");
    header("Location: income_expenses.php");
    exit;
}

// Handle update
if (isset($_POST['edit_entry'])) {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $income = $_POST['income'] ?: 0;
    $expense = $_POST['expense'] ?: 0;
    $stmt = $conn->prepare("UPDATE income_expense SET date=?, description=?, income=?, expense=? WHERE id=?");
    $stmt->bind_param("ssddi", $date, $description, $income, $expense, $id);
    $stmt->execute();
    header("Location: income_expenses.php");
    exit;
}

// Group by date
$dailySummary = $conn->query("SELECT date, SUM(income) AS total_income, SUM(expense) AS total_expense, SUM(income) - SUM(expense) AS balance FROM income_expense GROUP BY date ORDER BY date DESC");

$selectedDate = $_GET['view_date'] ?? null;
if ($selectedDate) {
    $stmt = $conn->prepare("SELECT * FROM income_expense WHERE date = ? ORDER BY id ASC");
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $entries = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ระบบรายรับรายจ่าย</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9fafc;
    }

    .nav-item a {
        color: white;
        margin-right: 1rem;
    }

    .navbar {
        padding: 10px;
    }

    .nav-link:hover {
        color: white;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #004085; padding-left: 2rem;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">แดชอบร์ด</a>
            <button class="navbar-toggler text-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">แดชอบร์ด</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=".php">ข้อมูลส่วนตัว</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="p-4">
        <div class="container">
            <h2 class="mb-4">ระบบรายรับ-รายจ่าย</h2>

            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+
                เพิ่มข้อมูล</button>

            <h5>สรุปรายวัน</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>รายรับ</th>
                        <th>รายจ่าย</th>
                        <th>คงเหลือ</th>
                        <th>ดูรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $dailySummary->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['date'] ?></td>
                        <td><?= number_format($row['total_income'], 2) ?></td>
                        <td><?= number_format($row['total_expense'], 2) ?></td>
                        <td><?= number_format($row['balance'], 2) ?></td>
                        <td><a href="?view_date=<?= $row['date'] ?>" class="btn btn-sm btn-info">ดู</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php if($selectedDate): ?>
            <h5>รายการวันที่ <?= $selectedDate ?></h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>รายการ</th>
                        <th>รายรับ</th>
                        <th>รายจ่าย</th>
                        <th>คงเหลือ (สะสม)</th>
                        <th>แก้ไข</th>
                        <th>ลบ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $running_balance = 0;
                    foreach ($entries as $entry): 
                        $running_balance += $entry['income'] - $entry['expense'];
                    ?>
                    <tr>
                        <td><?= $entry['date'] ?></td>
                        <td><?= $entry['description'] ?></td>
                        <td><?= number_format($entry['income'], 2) ?></td>
                        <td><?= number_format($entry['expense'], 2) ?></td>
                        <td><?= number_format($running_balance, 2) ?></td>
                        <td><button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $entry['id'] ?>">แก้ไข</button></td>
                        <td><a href="?delete=<?= $entry['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('ลบรายการนี้?')">ลบ</a></td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $entry['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">แก้ไขรายการ</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                    <input type="date" name="date" class="form-control mb-2"
                                        value="<?= $entry['date'] ?>" required>
                                    <input type="text" name="description" class="form-control mb-2"
                                        value="<?= $entry['description'] ?>" required>
                                    <input type="number" step="0.01" name="income" class="form-control mb-2"
                                        value="<?= $entry['income'] ?>">
                                    <input type="number" step="0.01" name="expense" class="form-control mb-2"
                                        value="<?= $entry['expense'] ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit_entry" class="btn btn-primary">บันทึก</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มรายการใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="date" name="date" class="form-control mb-2" required>
                    <input type="text" name="description" class="form-control mb-2" placeholder="รายละเอียด" required>
                    <input type="number" step="0.01" name="income" class="form-control mb-2" placeholder="รายรับ">
                    <input type="number" step="0.01" name="expense" class="form-control mb-2" placeholder="รายจ่าย">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_entry" class="btn btn-success">เพิ่มข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>