<?php
/**
 * Employee Model - Handles employee CRUD operations
 */

require_once __DIR__ . '/../config/database.php';

class EmployeeModel {
    private $db;
    private $table = 'mf_employees';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all employees with pagination
     */
    public function getAll($page = 1, $perPage = 10, $search = '') {
        $offset = ($page - 1) * $perPage;

        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE employee_id LIKE :search OR employee_name LIKE :search2";
            $params['search'] = "%$search%";
            $params['search2'] = "%$search%";
        }

        $sql = "SELECT recid, employee_id, employee_name, salary, birth_date
                FROM {$this->table}
                $whereClause
                ORDER BY recid DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get total count of employees
     */
    public function getTotalCount($search = '') {
        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE employee_id LIKE :search OR employee_name LIKE :search2";
            $params['search'] = "%$search%";
            $params['search2'] = "%$search%";
        }

        $sql = "SELECT COUNT(*) FROM {$this->table} $whereClause";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Get employee by ID
     */
    public function getById($recid) {
        $stmt = $this->db->prepare("SELECT recid, employee_id, employee_name, salary, birth_date FROM {$this->table} WHERE recid = :recid LIMIT 1");
        $stmt->execute(['recid' => $recid]);
        return $stmt->fetch();
    }

    /**
     * Generate next employee ID (EMP-0001 format)
     */
    public function generateEmployeeId() {
        $stmt = $this->db->query("SELECT employee_id FROM {$this->table} WHERE employee_id LIKE 'EMP-%' ORDER BY employee_id DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();

        if ($lastId) {
            $number = (int)substr($lastId, 4);
            $nextNumber = $number + 1;
        } else {
            $nextNumber = 1;
        }

        return 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if employee_id exists (excluding specific recid for updates)
     */
    public function employeeIdExists($employeeId, $excludeRecid = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE employee_id = :employee_id";
        $params = ['employee_id' => $employeeId];

        if ($excludeRecid !== null) {
            $sql .= " AND recid != :recid";
            $params['recid'] = $excludeRecid;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Add new employee
     */
    public function add($employeeId, $employeeName, $salary, $birthDate = null) {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (employee_id, employee_name, salary, birth_date)
             VALUES (:employee_id, :employee_name, :salary, :birth_date)"
        );

        return $stmt->execute([
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'salary' => number_format((float)$salary, 2, '.', ''),
            'birth_date' => $birthDate ?: null
        ]);
    }

    /**
     * Update employee
     */
    public function update($recid, $employeeId, $employeeName, $salary, $birthDate = null) {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET employee_id = :employee_id, employee_name = :employee_name, salary = :salary, birth_date = :birth_date
             WHERE recid = :recid"
        );

        return $stmt->execute([
            'recid' => $recid,
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'salary' => number_format((float)$salary, 2, '.', ''),
            'birth_date' => $birthDate ?: null
        ]);
    }

    /**
     * Delete employee
     */
    public function delete($recid) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE recid = :recid");
        return $stmt->execute(['recid' => $recid]);
    }

    /**
     * Validate employee data
     */
    public function validate($data, $recid = null) {
        $errors = [];

        if (empty($data['employee_id'])) {
            $errors[] = 'Employee ID is required.';
        } elseif (!preg_match('/^EMP-\d{4,}$/', $data['employee_id'])) {
            $errors[] = 'Employee ID must be in format EMP-XXXX (e.g., EMP-0001).';
        } elseif ($this->employeeIdExists($data['employee_id'], $recid)) {
            $errors[] = 'Employee ID already exists.';
        }

        if (empty($data['employee_name'])) {
            $errors[] = 'Employee name is required.';
        } elseif (strlen($data['employee_name']) > 255) {
            $errors[] = 'Employee name must not exceed 255 characters.';
        }

        if (!isset($data['salary']) || $data['salary'] === '') {
            $errors[] = 'Salary is required.';
        } elseif (!is_numeric($data['salary']) || $data['salary'] < 0) {
            $errors[] = 'Salary must be a valid positive number.';
        }

        return $errors;
    }
}
