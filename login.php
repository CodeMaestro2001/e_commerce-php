<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if admin login
    if ($username == 'admin' && $password == 'admin123') {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    }

    // For regular user login
    $query = "SELECT * FROM users WHERE username = ? AND password = ? AND active = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['role'] = 'user';
        $_SESSION['username'] = $user['username'];
        header("Location: user_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Login</title>
</head>
<body>
<div class="container mt-5">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
