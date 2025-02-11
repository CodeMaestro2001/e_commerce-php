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
    <title>Login - GFLOCK Fashion</title>
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header img {
            width: 120px;
            margin-bottom: 20px;
        }

        .login-header h2 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .login-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-floating {
            margin-bottom: 20px;
            position: relative;
        }

        .form-floating input {
            height: 60px;
            border-radius: 12px;
            border: 2px solid #e1e1e1;
            padding: 20px 15px 15px 45px;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-floating input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
            outline: none;
        }

        .form-floating label {
            position: absolute;
            left: 45px;
            top: 20px;
            color: #666;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-floating input:focus ~ label,
        .form-floating input:not(:placeholder-shown) ~ label {
            top: 8px;
            left: 45px;
            font-size: 0.8rem;
            color: #007bff;
        }

        .form-icon {
            position: absolute;
            left: 15px;
            top: 20px;
            color: #666;
            transition: all 0.3s ease;
        }

        .form-floating input:focus ~ .form-icon {
            color: #007bff;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
        }

        .forgot-password {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            background: #007bff;
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .social-login {
            margin-top: 30px;
            text-align: center;
        }

        .social-login p {
            color: #666;
            margin-bottom: 15px;
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #ddd;
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e1e1e1;
            color: #666;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: #007bff;
            border-color: #007bff;
            color: white;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: none;
            background-color: #f8d7da;
            color: #721c24;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
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

            <button type="submit" class="login-btn">
                Log In
            </button>

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