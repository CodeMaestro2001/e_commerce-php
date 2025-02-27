<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    private $conn;
    
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // Check if user is admin (type=3)
    public function isAdmin() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Get user type from database
        $user_id = $_SESSION['user_id'];
        $query = "SELECT type FROM users WHERE id = ? AND active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return intval($row['type']) === 3;  // Admin type is 3
        }
        
        return false;
    }

    // Check if user is a registered user (type=2)
    public function isRegisteredUser() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Get user type from database
        $user_id = $_SESSION['user_id'];
        $query = "SELECT type FROM users WHERE id = ? AND active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return intval($row['type']) === 2;  // Regular user type is 2
        }
        
        return false;
    }

    // Verify page access permissions - This method runs on every page load
    public function checkPermission($page) {
        // Get the current page name
        $currentPage = basename($page);
        
        $adminPages = [
            'admin_dashboard.php',
            'admin_add_product.php',
            'admin_view_products.php',
            'admin_edit_product.php',
            'admin_delete_product.php',
            'admin_users.php',
            'admin_orders.php'
        ];

        $userPages = [
            'user_dashboard.php',
            'view_products.php',
            'cart.php',
            'orders.php',
            'profile.php'
        ];

        // Force check for admin pages
        if (in_array($currentPage, $adminPages)) {
            // This is the critical security check - verify admin status
            if (!$this->isAdmin()) {
                $this->redirectUnauthorized();
            }
        }

        // Force check for user pages
        if (in_array($currentPage, $userPages)) {
            if (!$this->isLoggedIn()) {
                $this->redirectLogin();
            }
        }
    }

    // Redirect unauthorized access
    private function redirectUnauthorized() {
        $_SESSION['error'] = "Access Denied. Only administrators can access this area.";
        header("Location: login.php");
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
        $query = "SELECT id, username, email, type, active, created_at FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

// Include the database connection
require_once 'config.php';
$auth = new Auth($conn);

// IMMEDIATELY check permissions for current page
// This is crucial - it will redirect unauthorized users right away
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