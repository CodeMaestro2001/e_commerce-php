<?php
// Start session at the very beginning of the script
session_start();

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate password strength (at least 8 chars, 1 uppercase, 1 number)
    if (!preg_match("/^(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
        $error = "Password must be at least 8 characters long, contain an uppercase letter, and a number.";
        $_SESSION['error_message'] = $error; // Store error in session
    }
    
    else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if username or email already exists
        $checkUserQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($checkUserQuery);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists!";
            $_SESSION['error_message'] = $error; // Store error in session
        } else {
            // Insert new user
            $insertQuery = "INSERT INTO users (username, email, password, role, active) VALUES (?, ?, ?, 'user', 1)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $success = "Account created successfully! Redirecting to login...";
                $_SESSION['success_message'] = $success; // Store success message in session
                
                // Store new user's ID in session
                $user_id = $conn->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'user';
                
                // Set a session welcome flag for first-time login
                $_SESSION['new_registration'] = true;
                
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                      </script>";
            } else {
                $error = "Error: " . $conn->error;
                $_SESSION['error_message'] = $error; // Store error in session
            }
        }
        $stmt->close();
    }
}

// Get messages from session if they exist
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear after use
}

if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear after use
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Register - Dione Fashion</title>
    <link rel="stylesheet" href="css/style.css"> <!-- External CSS -->
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h2>Create Account</h2>
            <p>Join Dione and discover the latest fashion trends</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="needs-validation" novalidate>
            <div class="form-floating">
                <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <div class="invalid-feedback">Please choose a username.</div>
            </div>

            <div class="form-floating">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                <label for="email"><i class="fas fa-envelope"></i> Email address</label>
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>

            <div class="form-floating">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="password-requirements">
                    Password must contain at least 8 characters, including uppercase and numbers.
                </div>
            </div>

            <button type="submit" class="register-btn">Create Account</button>

            <div class="login-link">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Form validation
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => { 
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php include 'footer.php'; ?>
</body>
</html>