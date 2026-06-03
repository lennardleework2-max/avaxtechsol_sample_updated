<?php
/**
 * Database Setup Script
 * Run this once to create tables and default admin user
 * DELETE THIS FILE AFTER SETUP FOR SECURITY
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

$messages = [];
$errors = [];

try {
    $db = Database::getInstance()->getConnection();

    // Create mf_users table
    $sql = "CREATE TABLE IF NOT EXISTS mf_users (
        recid INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $db->exec($sql);
    $messages[] = "Table 'mf_users' created or already exists.";

    // Create mf_employees table
    $sql = "CREATE TABLE IF NOT EXISTS mf_employees (
        recid INT AUTO_INCREMENT PRIMARY KEY,
        employee_id VARCHAR(20) NOT NULL UNIQUE,
        employee_name VARCHAR(255) NOT NULL,
        salary DECIMAL(15,2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_employee_id (employee_id),
        INDEX idx_employee_name (employee_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $db->exec($sql);
    $messages[] = "Table 'mf_employees' created or already exists.";

    // Check if admin user exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM mf_users WHERE username = ?");
    $stmt->execute(['admin']);
    $adminExists = $stmt->fetchColumn() > 0;

    if (!$adminExists) {
        // Create default admin user with password 'admin123'
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO mf_users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
        $messages[] = "Default admin user created. Username: admin, Password: admin123";
    } else {
        $messages[] = "Admin user already exists.";
    }

    // Insert sample employees if table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM mf_employees");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $employees = [
            ['EMP-0001', 'John Doe', 50000.00],
            ['EMP-0002', 'Jane Smith', 55000.00],
            ['EMP-0003', 'Bob Johnson', 45000.00],
            ['EMP-0004', 'Alice Williams', 60000.00],
            ['EMP-0005', 'Charlie Brown', 52000.00],
        ];

        $stmt = $db->prepare("INSERT INTO mf_employees (employee_id, employee_name, salary) VALUES (?, ?, ?)");
        foreach ($employees as $emp) {
            $stmt->execute($emp);
        }
        $messages[] = "Sample employees inserted.";
    } else {
        $messages[] = "Employees table already has data.";
    }

} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Database Setup</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5>Errors:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($messages)): ?>
                            <div class="alert alert-success">
                                <h5>Setup Results:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($messages as $msg): ?>
                                        <li><?php echo htmlspecialchars($msg); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($errors)): ?>
                            <div class="alert alert-warning">
                                <strong>Security Notice:</strong> Delete this file (setup.php) after completing the setup.
                            </div>

                            <h5>Default Login Credentials:</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Username:</th>
                                    <td><code>admin</code></td>
                                </tr>
                                <tr>
                                    <th>Password:</th>
                                    <td><code>admin123</code></td>
                                </tr>
                            </table>

                            <a href="index.php" class="btn btn-primary">Go to Application</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
