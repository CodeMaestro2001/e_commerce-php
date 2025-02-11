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
    <title>Register - Dione Fashion</title>
    <style>
        .register-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 40px 20px;
        }

        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            margin: 0 auto;
        }

        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .register-header h2 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .register-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating input {
            border-radius: 10px;
            padding: 15px;
            height: 60px;
            border: 2px solid #e1e1e1;
            transition: all 0.3s ease;
        }

        .form-floating input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .form-floating label {
            padding: 20px;
        }

        .register-btn {
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            background: #007bff;
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .register-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }

        .password-requirements {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
            padding-left: 15px;
        }

        .form-icon {
            color: #007bff;
            font-size: 1.2rem;
            margin-right: 10px;
        }
    </style>
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
                <i class="fas fa-exclamation-circle form-icon"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="needs-validation" novalidate>
            <div class="form-floating">
                <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                <label for="username"><i class="fas fa-user form-icon"></i>Username</label>
                <div class="invalid-feedback">Please choose a username.</div>
            </div>

            <div class="form-floating">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                <label for="email"><i class="fas fa-envelope form-icon"></i>Email address</label>
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>

            <div class="form-floating">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <label for="password"><i class="fas fa-lock form-icon"></i>Password</label>
                <div class="password-requirements">
                    Password must contain at least 8 characters, including uppercase, lowercase letters and numbers
                </div>
            </div>

            <button type="submit" class="register-btn">
                Create Account
            </button>

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
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => { 
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<?php include 'footer.php'; ?>
</body>
</html>