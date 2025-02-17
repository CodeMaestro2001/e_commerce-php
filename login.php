<?php
session_start();
include 'config.php'; // Ensure database connection is included

// Check if the database connection is working
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Debugging: Print Entered Credentials
    echo "Entered Username: " . htmlspecialchars($username) . "<br>";
    echo "Entered Password: " . htmlspecialchars($password) . "<br>";

    // Fetch user details from the database
    $query = "SELECT * FROM users WHERE username = ? AND active = 1";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("SQL Preparation Failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Debugging: Print Stored Hashed Password
        echo "Hashed Password from DB: " . $user['password'] . "<br>";

        // Debugging: Print Entered Password Hash
        $enteredPasswordHash = password_hash($password, PASSWORD_DEFAULT);
        echo "Entered Password Hash (for reference): " . $enteredPasswordHash . "<br>";

        // Verify the password
        if (password_verify($password, $user['password'])) {
            echo "✅ Password Matched! Logging in...<br>";

            // Set session variables
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            echo "❌ Password Mismatch! Please check credentials.<br>";
            $error = "Invalid username or password!";
        }
    } else {
        echo "❌ User Not Found or Inactive!<br>";
        $error = "Invalid username or password!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Login - Dione Fashion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <img src="images/dione_logo.png" alt="DIONE Logo">
            <h2>Welcome Back</h2>
            
            <p>Please log in to your account</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-floating">
                <i class="fas fa-user form-icon"></i>
                <input type="text" name="username" id="username" placeholder=" " required>
                <label for="username">Username</label>
            </div>

            <div class="form-floating">
                <i class="fas fa-lock form-icon"></i>
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Password</label>
            </div>

            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="login-btn">Log In</button>

            <div class="register-link">
                Don't have an account?<a href="register.php">Sign up</a>
            </div>

            <div class="social-login">
                <p>Or continue with</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-google"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
