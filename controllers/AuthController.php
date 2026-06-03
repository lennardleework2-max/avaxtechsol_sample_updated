<?php
/**
 * Auth Controller - Handles login/logout
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Show login page
     */
    
    public function showLogin() {
        if (isLoggedIn()) {
            header('Location: index.php?action=employee.list');
            exit;
        }

        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/login.php';
    }

    /**
     * Process login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=auth.login');
            exit;
        }

        $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCsrfToken($csrfToken)) {
            $error = 'Invalid request. Please try again.';
            $csrfToken = generateCsrfToken();
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        $username = isset($_POST['username']) ? sanitizeInput($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
            $csrfToken = generateCsrfToken();
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            session_regenerate_id(true);
            header('Location: index.php?action=employee.list');
            exit;
        } else {
            $error = 'Invalid username or password.';
            $csrfToken = generateCsrfToken();
            require_once __DIR__ . '/../views/login.php';
        }
    }

    /**
     * Process logout
     */
    public function logout() {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        header('Location: index.php?action=auth.login');
        exit;
    }
}
