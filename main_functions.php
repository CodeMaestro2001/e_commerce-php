<?php
session_start();

class Auth {
    private $conn;
    
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }

    // Check if user is admin
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    // Check if user is a registered user
    public function isRegisteredUser() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'user';
    }

    // Verify page access permissions
    public function checkPermission($page) {
        $adminPages = [
            'admin_dashboard.php',
            'add_product.php',
            'admin_view_products.php',
            'edit_product.php',
            'delete_product.php'
        ];

        $userPages = [
            'user_dashboard.php',
            'view_products.php',
            'cart.php',
            'orders.php',
            'profile.php'
        ];

        $publicPages = [
            'index.php',
            'login.php',
            'register.php',
            'product_list.php',
            'contact.php'
        ];

        // Get the current page name
        $currentPage = basename($_SERVER['PHP_SELF']);

        if (in_array($currentPage, $adminPages) && !$this->isAdmin()) {
            $this->redirectUnauthorized();
        }

        if (in_array($currentPage, $userPages) && !$this->isLoggedIn()) {
            $this->redirectLogin();
        }
    }

    // Redirect unauthorized access
    private function redirectUnauthorized() {
        $_SESSION['error'] = "You don't have permission to access this page.";
        header("Location: index.php");
        exit();
    }

    // Redirect to login
    private function redirectLogin() {
        $_SESSION['error'] = "Please login to access this page.";
        header("Location: login.php");
        exit();
    }

    // Log out user
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // Get user details
    public function getUserDetails($user_id) {
        $query = "SELECT id, username, email, role, created_at FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

// Initialize the Auth class
require_once 'config.php';
$auth = new Auth($conn);

// Check permissions for current page
$auth->checkPermission($_SERVER['PHP_SELF']);

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to verify CSRF token
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    return true;
}