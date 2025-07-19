<?php
session_start();
include 'db.php';

// ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$permissions = $_SESSION['permissions'];
$fullname = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชอบร์ด</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
    body {
        font-family: 'Prompt', sans-serif;
        height: auto;
        background-color: #96a1cd;
        margin: 0;
    }

    .card {
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
        background: white;
        margin-top: 50px;
        margin: 3% 5%;
        transition: 0.3s;
        background-color: #004085;
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

    .container {
        background: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 700px;
        margin: 20px;
    }

    h2 {
        margin-bottom: 20px;
        color: black;
        text-align: center;
        margin-top: 20px;
    }

    button {
        width: 48%;
        padding: 12px;
        font-size: 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .container-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 56px);
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #004085; padding-left: 2rem;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">dashboard</a>
            <button class="navbar-toggler text-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="parcel_management.php">จัดการพัสดุ</a>
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

    <div class="container-wrapper">
        <div class="container">
            <h2 class="text-center mb-4">Hi <?php echo htmlspecialchars($fullname); ?></h2>

            <div class="row">
                <?php if (in_array('income_expenses', $permissions)): ?>
                <div class="col-md-6 mb-3">
                    <a href="income_expenses.php" class="text-decoration-none">
                        <div class="card text-center p-4 text-white">
                            <h4>รายรับ-รายจ่าย</h4>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
                <?php if (in_array('shop', $permissions)): ?>
                <div class="col-md-6 mb-3">
                    <a href="shop.php" class="text-decoration-none">
                        <div class="card text-center p-4  text-white">
                            <h4>ร้านค้า</h4>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
                <?php if (in_array('check_mood', $permissions)): ?>
                <div class="col-md-6 mb-3">
                    <a href="check_mood.php" class="text-decoration-none">
                        <div class="card text-center p-4 text-white">
                            <h4>เช็ต mood</h4>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
                <?php if (in_array('todo_list', $permissions)): ?>
                <div class="col-md-6 mb-3">
                    <a href="todo_list.php" class="text-decoration-none">
                        <div class="card text-center p-4 text-white">
                            <h4>Todo list</h4>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>

    </script>

</body>

</html>