<?php
session_start();
include 'config.php'; // Ensure database connection is included

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on role
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Check if there's a registration success message
if (isset($_SESSION['new_registration']) && $_SESSION['new_registration'] === true) {
    $success = "Your account has been created successfully! Please log in.";
    unset($_SESSION['new_registration']); // Clear the flag after use
}

// Check if the database connection is working
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle "remember me" from previous sessions
if (!isset($_SESSION['user_id']) && isset($_COOKIE['dione_remember_user'])) {
    $token = $_COOKIE['dione_remember_user'];
    
    // Validate the remember me token from database (you'll need to create this table)
    $query = "SELECT user_id FROM remember_tokens WHERE token = ? AND expires > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        
        // Get user details
        $userQuery = "SELECT * FROM users WHERE id = ? AND active = 1";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("i", $user_id);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        
        if ($userResult->num_rows > 0) {
            $user = $userResult->fetch_assoc();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

    // Remove debugging outputs for production
    // echo "Entered Username: " . htmlspecialchars($username) . "<br>";
    // echo "Entered Password: " . htmlspecialchars($password) . "<br>";

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

        // Remove debugging outputs for production
        // echo "Hashed Password from DB: " . $user['password'] . "<br>";
        // $enteredPasswordHash = password_hash($password, PASSWORD_DEFAULT);
        // echo "Entered Password Hash (for reference): " . $enteredPasswordHash . "<br>";

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // echo "✅ Password Matched! Logging in...<br>";

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Set a session flag to show a welcome message on the dashboard
            $_SESSION['fresh_login'] = true;
            
            // Log login attempt in a sessions_log table (you'll need to create this)
            $ip = $_SERVER['REMOTE_ADDR'];
            $browser = $_SERVER['HTTP_USER_AGENT'];
            $logQuery = "INSERT INTO sessions_log (user_id, login_time, ip_address, browser) VALUES (?, NOW(), ?, ?)";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param("iss", $user['id'], $ip, $browser);
            $logStmt->execute();
            
            // Handle "Remember Me" functionality
            if ($remember) {
                // Generate a secure token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Store token in database (you'll need to create this table)
                $tokenQuery = "INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)";
                $tokenStmt = $conn->prepare($tokenQuery);
                $tokenStmt->bind_param("iss", $user['id'], $token, $expires);
                $tokenStmt->execute();
                
                // Set cookie with the token
                setcookie('dione_remember_user', $token, time() + (86400 * 30), "/", "", true, true); // 30 days, secure, httponly
            }

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            // echo "❌ Password Mismatch! Please check credentials.<br>";
            $error = "Invalid username or password!";
            
            // You could log failed attempts here
            $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;
            
            // If too many failed attempts, you could implement a lockout
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['lockout_time'] = time() + 900; // 15 minutes lockout
                $error = "Too many failed attempts. Please try again in 15 minutes.";
            }
        }
    } else {
        // echo "❌ User Not Found or Inactive!<br>";
        $error = "Invalid username or password!";
        
        // Track failed attempts in session
        $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;
    }

    $stmt->close();
}

// Check for lockout
if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
    $remaining = $_SESSION['lockout_time'] - time();
    $error = "Account is temporarily locked. Please try again in " . ceil($remaining/60) . " minutes.";
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
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
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
                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="login-btn">Log In</button>

            <div class="register-link">
                Don't have an account? <a href="register.php">Sign up</a>
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