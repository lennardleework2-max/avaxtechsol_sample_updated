<?php
/**
 * Application Routes
 *
 * Format: 'action.name' => ['ControllerClass', 'methodName']
 *
 * Usage:
 *   - URL: index.php?action=employee.list
 *   - This calls: EmployeeController->list()
 */

return [

    // =============================================
    // AUTH
    // =============================================
    'auth.login'           => ['AuthController', 'showLogin'],
    'auth.do.login'        => ['AuthController', 'login'],
    'auth.logout'          => ['AuthController', 'logout'],

    // =============================================
    // DASHBOARD
    // =============================================
    'dashboard'            => ['DashboardController', 'index'],

    // =============================================
    // EMPLOYEES
    // =============================================
    'employee.list'        => ['EmployeeController', 'list'],
    'employee.get'         => ['EmployeeController', 'get'],
    'employee.get.all'     => ['EmployeeController', 'getAll'],
    'employee.add'         => ['EmployeeController', 'add'],
    'employee.edit'        => ['EmployeeController', 'edit'],
    'employee.delete'      => ['EmployeeController', 'delete'],
    'employee.generate.id' => ['EmployeeController', 'generateId'],

];
