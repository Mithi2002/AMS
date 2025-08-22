<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect by role
            if ($user['role'] == 'admin') {
                header("Location: ../Index/index.html");
            } elseif ($user['role'] == 'advocate') {
                header("Location: Advocate/advocate-dashboard.html");
            } else {
                header("Location: Citizen/citizen_dashboard.html");
            }
            exit();
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Login - Advocate & Criminology Management System - আইনপ্রহরী</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #1a202c;
            --secondary-blue: #2c5282;
            --accent-blue: #3182ce;
            --light-blue: #ebf8ff;
            --text-dark: #2d3748;
            --text-medium: #4a5568;
            --text-light: #718096;
            --bg-light: #f7fafc;
            --bg-white: #ffffff;
            --bg-dark: #1a202c;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding-top: 76px;
            overflow-x: hidden;
        }
        .navbar { box-shadow: 0 2px 5px rgba(0,0,0,0.08); }
        .navbar-brand { color: var(--primary-blue)!important; font-weight:800; font-size:1.75rem; border-radius:.5rem; padding:.5rem .75rem; transition:.3s; }
        .navbar-brand:hover { background-color: var(--light-blue); }
        .nav-link { color: var(--text-light)!important; font-weight:500; transition:.3s; }
        .nav-link:hover { color: var(--accent-blue)!important; }
        .btn-primary-custom { background-color: var(--accent-blue); color:white; border:none; padding:.75rem 2rem; border-radius:9999px; box-shadow:0 4px 10px rgba(0,0,0,0.15); transition:.3s; }
        .btn-primary-custom:hover { background-color:#2b6cb0; transform:translateY(-2px); color:white; }
        .btn-secondary-outline-custom { background:transparent; color:var(--text-medium); border:1px solid var(--text-light); padding:.5rem 1rem; border-radius:.5rem; transition:.3s; }
        .btn-secondary-outline-custom:hover { background:var(--light-blue); color:var(--accent-blue); border-color:var(--accent-blue); }
        .login-container { min-height:calc(100vh - 76px - 64px); display:flex; align-items:center; justify-content:center; padding:2rem 0; background-color: var(--light-blue); }
        .login-card { background:var(--bg-white); padding:2.5rem; border-radius:.75rem; box-shadow:0 5px 15px rgba(0,0,0,0.08); border:1px solid #f0f0f0; max-width:450px; width:100%; }
        .form-control { border-radius:.5rem; padding:.75rem 1rem; }
        .form-control:focus { border-color:var(--accent-blue); box-shadow:0 0 0 .25rem rgba(49,130,206,.25); }
        .text-primary-custom { color: var(--accent-blue)!important; }
        .bg-gray-900-custom { background-color: var(--bg-dark); }
        footer a { transition:.3s; } footer a:hover { color:white!important; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <i data-lucide="gavel" class="me-2 text-primary-custom-icon"></i>
                <span id="app-name">আইনপ্রহরী</span>
            </a>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="login-container">
        <div class="login-card text-center">
            <h2 class="h3 fw-bold mb-4">লগইন করুন</h2>
            <?php if ($message) echo "<p class='text-danger'>$message</p>"; ?>
            <form method="POST" action="">
                <div class="mb-3 text-start">
                    <label class="form-label">ইমেল / আইডি</label>
                    <input type="email" name="email" required class="form-control" placeholder="আপনার ইমেল বা আইডি লিখুন">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">পাসওয়ার্ড</label>
                    <input type="password" name="password" required class="form-control" placeholder="আপনার পাসওয়ার্ড লিখুন">
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">আমাকে মনে রাখুন</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 mb-3">প্রবেশ করুন</button>
                <p class="text-muted">আপনার কি অ্যাকাউন্ট নেই? <a href="signup.php" class="text-primary-custom text-decoration-none">নিবন্ধন করুন</a></p>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900-custom text-light py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> আইনপ্রহরী. সর্বস্বত্ব সংরক্ষিত।</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
