<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $role = 'user'; // Default role

    // Check if username or email already exists
    $checkUser = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($checkUser);

    if ($result->num_rows > 0) {
        $error = "Username or Email already exists!";
    } else {
        // Insert user into database
        $sql = "INSERT INTO users (username, email, password, role, active) VALUES ('$username', '$email', '$password', '$role', 1)";
        if ($conn->query($sql)) {
            header("Location: login.php"); // Redirect to login page
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Register</title>
</head>
<body>
<div class="container mt-5">
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
