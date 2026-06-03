<?php
/**
 * Dashboard Controller - Handles dashboard/home page
 */

require_once __DIR__ . '/../config/session.php';

class DashboardController
{
    /**
     * Display dashboard or redirect appropriately
     */
    public function index()
    {
        if (isLoggedIn()) {
            header('Location: index.php?action=employee.list');
        } else {
            header('Location: index.php?action=auth.login');
        }
        exit;
    }
}
