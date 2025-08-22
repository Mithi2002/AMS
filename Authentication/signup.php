<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role       = $_POST['role'];
    $full_name  = trim($_POST['full_name']);
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);

    // Insert into users table
    $sql = "INSERT INTO users (username, password, role, email, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $password, $role, $email, $phone);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Role-specific profile
        if ($role == "admin") {
            $sql2 = "INSERT INTO admin_profiles (user_id, full_name, email, phone, address) VALUES (?, ?, ?, ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("issss", $user_id, $full_name, $email, $phone, $address);
        } elseif ($role == "advocate") {
            $sql2 = "INSERT INTO advocate_profiles (user_id, full_name, specialization, bar_registration_no, email, phone) 
                     VALUES (?, ?, '', '', ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("issss", $user_id, $full_name, $email, $phone);
        } else {
            $sql2 = "INSERT INTO citizen_profiles (user_id, full_name, email, phone, address, nid_number) 
                     VALUES (?, ?, ?, ?, ?, '')";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("issss", $user_id, $full_name, $email, $phone, $address);
        }

        $stmt2->execute();
        $message = "✅ Registration successful. <a href='login.php'>Login here</a>";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Advocate & Criminology Management System - আইনপ্রহরী</title>
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
            --lighter-blue: #e0f2fe;
            --text-dark: #2d3748;
            --text-medium: #4a5568;
            --text-light: #718096;
            --bg-light: #f7fafc;
            --bg-white: #ffffff;
            --bg-dark: #1a202c;
        }
        body { font-family:'Inter',sans-serif; background-color:var(--bg-light); color:var(--text-dark); padding-top:76px; overflow-x:hidden; }
        .navbar { box-shadow:0 2px 5px rgba(0,0,0,.08); }
        .navbar-brand { color:var(--primary-blue)!important; font-weight:800; font-size:1.75rem; border-radius:.5rem; padding:.5rem .75rem; transition:.3s; }
        .navbar-brand:hover { background:var(--light-blue); }
        .nav-link { color:var(--text-light)!important; font-weight:500; transition:.3s; }
        .nav-link:hover { color:var(--accent-blue)!important; }
        .btn-primary-custom { background:var(--accent-blue); color:white; border:none; padding:.75rem 2rem; border-radius:9999px; box-shadow:0 4px 10px rgba(0,0,0,.15); transition:.3s; }
        .btn-primary-custom:hover { background:#2b6cb0; transform:translateY(-2px); }
        .btn-secondary-outline-custom { background:transparent; color:var(--text-medium); border:1px solid var(--text-light); padding:.5rem 1rem; border-radius:.5rem; transition:.3s; }
        .btn-secondary-outline-custom:hover { background:var(--light-blue); color:var(--accent-blue); border-color:var(--accent-blue); }
        .signup-container { min-height:calc(100vh - 76px - 64px); display:flex; align-items:center; justify-content:center; padding:2rem 0; background-color:var(--light-blue);}
        .signup-card { background:var(--bg-white); padding:2.5rem; border-radius:.75rem; box-shadow:0 5px 15px rgba(0,0,0,.08); border:1px solid #f0f0f0; max-width:550px; width:100%; }
        .form-control,.form-select { border-radius:.5rem; padding:.75rem 1rem; }
        .form-control:focus,.form-select:focus { border-color:var(--accent-blue); box-shadow:0 0 0 .25rem rgba(49,130,206,.25); }
        .text-primary-custom { color:var(--accent-blue)!important; }
        .bg-gray-900-custom { background-color:var(--bg-dark); }
        footer a { transition:.3s; } footer a:hover{color:white!important;}
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

    <!-- Signup Section -->
    <section class="signup-container">
        <div class="signup-card text-center">
            <h2 class="h3 fw-bold mb-4">নিবন্ধন করুন</h2>
            <?php if ($message) echo "<p class='text-danger'>$message</p>"; ?>
            <form method="POST" action="">
                <div class="mb-3 text-start">
                    <label class="form-label">পুরো নাম</label>
                    <input type="text" name="full_name" required class="form-control" placeholder="আপনার পুরো নাম লিখুন">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">ইউজারনেম</label>
                    <input type="text" name="username" required class="form-control" placeholder="একটি ইউজারনেম দিন">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">ইমেল</label>
                    <input type="email" name="email" required class="form-control" placeholder="আপনার ইমেল লিখুন">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">ফোন নম্বর</label>
                    <input type="text" name="phone" class="form-control" placeholder="আপনার ফোন নম্বর লিখুন">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">ঠিকানা</label>
                    <textarea name="address" class="form-control" placeholder="আপনার ঠিকানা লিখুন"></textarea>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">পাসওয়ার্ড</label>
                    <input type="password" name="password" required class="form-control" placeholder="একটি পাসওয়ার্ড তৈরি করুন">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">আপনার ভূমিকা</label>
                    <select name="role" class="form-select" required>
                        <option value="citizen">নাগরিক</option>
                        <option value="advocate">আইনজীবী</option>
                        <option value="admin">প্রশাসক</option>
                    </select>
                    <small class="form-text text-muted">দ্রষ্টব্য: আইনজীবী ও প্রশাসক ভূমিকার জন্য যাচাইকরণ প্রয়োজন হবে।</small>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 mb-3">নিবন্ধন করুন</button>
                <p class="text-muted">আপনার কি ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="login.php" class="text-primary-custom text-decoration-none">লগইন করুন</a></p>
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
