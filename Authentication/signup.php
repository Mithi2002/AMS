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

    // 1. Insert into users table
    $sql = "INSERT INTO users (username, password, role, email, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $password, $role, $email, $phone);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // 2. Insert into role-specific profile
        if ($role == "admin") {
            $sql2 = "INSERT INTO admin_profiles (user_id, full_name, email, phone, address) 
                     VALUES (?, ?, ?, ?, ?)";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup - AMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4">
                <h3 class="text-center mb-3">Create Account</h3>
                <?php if ($message) echo "<p class='text-center text-danger'>$message</p>"; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="citizen">Citizen</option>
                            <option value="advocate">Advocate</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Signup</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
