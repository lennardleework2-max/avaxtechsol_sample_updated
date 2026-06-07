<?php
/**
 * Employee Controller - Handles employee CRUD operations
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/EmployeeModel.php';

class EmployeeController {
    private $employeeModel;

    public function __construct() {
        $this->employeeModel = new EmployeeModel();
    }

    /**
     * Display employee list page
     */
    public function list() {
        requireLogin();
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/employee/list.php';
    }

    /**
     * Get employees list (AJAX)
     * Alias: getAll() for route compatibility
     */
    public function getAll() {
        $this->getEmployees();
    }

    /**
     * Get employees list (AJAX)
     */
    public function getEmployees() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method.');
            return;
        }

        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $perPage = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 10;
        $search = isset($_POST['search']) ? sanitizeInput($_POST['search']) : '';

        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $employees = $this->employeeModel->getAll($page, $perPage, $search);
        $totalCount = $this->employeeModel->getTotalCount($search);
        $totalPages = ceil($totalCount / $perPage);

        $escapedEmployees = array_map(function($emp) {
            return [
                'recid' => (int)$emp['recid'],
                'employee_id' => escapeOutput($emp['employee_id']),
                'birth_date' => $emp['birth_date'] ? escapeOutput($emp['birth_date']) : '',
                'employee_name' => escapeOutput($emp['employee_name']),
                'salary' => number_format((float)$emp['salary'], 2)
            ];
        }, $employees);

        $this->jsonResponse(true, '', [
            'employees' => $escapedEmployees,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Get single employee (AJAX)
     * Alias: get() for route compatibility
     */
    public function get() {
        $this->getEmployee();
    }

    /**
     * Get single employee (AJAX)
     */
    public function getEmployee() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method.');
            return;
        }

        $recid = isset($_POST['recid']) ? (int)$_POST['recid'] : 0;

        if ($recid <= 0) {
            $this->jsonResponse(false, 'Invalid employee ID.');
            return;
        }

        $employee = $this->employeeModel->getById($recid);

        if (!$employee) {
            $this->jsonResponse(false, 'Employee not found.');
            return;
        }

        $this->jsonResponse(true, '', [
            'employee' => [
                'recid' => (int)$employee['recid'],
                'employee_id' => escapeOutput($employee['employee_id']),
                'employee_name' => escapeOutput($employee['employee_name']),
                'salary' => number_format((float)$employee['salary'], 2, '.', ''),
                'birth_date' => $employee['birth_date'] ? escapeOutput($employee['birth_date']) : ''
            ]
        ]);
    }

    /**
     * Generate next employee ID (AJAX)
     */
    public function generateId() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method.');
            return;
        }

        $employeeId = $this->employeeModel->generateEmployeeId();
        $this->jsonResponse(true, '', ['employee_id' => $employeeId]);
    }

    /**
     * Add new employee (AJAX)
     */
    public function add() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method.');
            return;
        }

        $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCsrfToken($csrfToken)) {
            $this->jsonResponse(false, 'Invalid request. Please refresh and try again.');
            return;
        }

        $data = [
            'employee_id' => isset($_POST['employee_id']) ? sanitizeInput($_POST['employee_id']) : '',
            'employee_name' => isset($_POST['employee_name']) ? sanitizeInput($_POST['employee_name']) : '',
            'salary' => isset($_POST['salary']) ? $_POST['salary'] : '',
            'birth_date' => isset($_POST['birth_date']) ? sanitizeInput($_POST['birth_date']) : ''
        ];

        $errors = $this->employeeModel->validate($data);

        if (!empty($errors)) {
            $this->jsonResponse(false, implode(' ', $errors));
            return;
        }

        $result = $this->employeeModel->add(
            $data['employee_id'],
            $data['employee_name'],
            $data['salary'],
            $data['birth_date']
        );

        if ($result) {
            $this->jsonResponse(true, 'Employee added successfully.');
        } else {
            $this->jsonResponse(false, 'Failed to add employee. Please try again.');
        }
    }

    /**
     * Edit employee (AJAX)
     */
    public function edit() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method.');
            return;
        }

        $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCsrfToken($csrfToken)) {
            $this->jsonResponse(false, 'Invalid request. Please refresh and try again.');
            return;
        }

        $recid = isset($_POST['recid']) ? (int)$_POST['recid'] : 0;

        if ($recid <= 0) {
            $this->jsonResponse(false, 'Invalid employee ID.');
            return;
        }

        $existing = $this->employeeModel->getById($recid);
        if (!$existing) {
            $this->jsonResponse(false, 'Employee not found.');
            return;
        }

        $data = [
            'employee_id' => isset($_POST['employee_id']) ? sanitizeInput($_POST['employee_id']) : '',
            'employee_name' => isset($_POST['employee_name']) ? sanitizeInput($_POST['employee_name']) : '',
            'salary' => isset($_POST['salary']) ? $_POST['salary'] : '',
            'birth_date' => isset($_POST['birth_date']) ? sanitizeInput($_POST['birth_date']) : ''
        ];

        $errors = $this->employeeModel->validate($data, $recid);

        if (!empty($errors)) {
            $this->jsonResponse(false, implode(' ', $errors));
            return;
        }

        $result = $this->employeeModel->update(
            $recid,
            $data['employee_id'],
            $data['employee_name'],
            $data['salary'],
            $data['birth_date']
        );

        if ($result) {
            $this->jsonResponse(true, 'Employee updated successfully.');
        } else {
            $this->jsonResponse(false, 'Failed to update employee. Please try again.');
        }
    }

    /**
     * Delete employee (AJAX)
     */
    public function delete() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method.');
            return;
        }

        $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCsrfToken($csrfToken)) {
            $this->jsonResponse(false, 'Invalid request. Please refresh and try again.');
            return;
        }

        $recid = isset($_POST['recid']) ? (int)$_POST['recid'] : 0;

        if ($recid <= 0) {
            $this->jsonResponse(false, 'Invalid employee ID.');
            return;
        }

        $existing = $this->employeeModel->getById($recid);
        if (!$existing) {
            $this->jsonResponse(false, 'Employee not found.');
            return;
        }

        $result = $this->employeeModel->delete($recid);

        if ($result) {
            $this->jsonResponse(true, 'Employee deleted successfully.');
        } else {
            $this->jsonResponse(false, 'Failed to delete employee. Please try again.');
        }
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($success, $message = '', $data = []) {
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'success' => $success,
            'message' => $message
        ], $data));
        exit;
    }
}
