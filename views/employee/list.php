<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List - Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 10px; }
        .table th { background-color: #f8f9fa; font-weight: 600; }
        .btn-action { padding: 5px 10px; margin: 0 2px; }
        .pagination { margin-bottom: 0; }
        .salary-col { text-align: right; }
        @media (max-width: 576px) {
            .table-responsive { font-size: 14px; }
            .btn-action { padding: 3px 6px; font-size: 12px; }
        }
        input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Employee Management</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-sm-inline">
                    <i class="bi bi-person-circle"></i>
                    <?php echo escapeOutput($_SESSION['username']); ?>
                </span>
                <a href="index.php?action=auth.logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <h5 class="mb-0"><i class="bi bi-people"></i> Employee List</h5>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button type="button" class="btn btn-primary" id="btnAddEmployee">
                            <i class="bi bi-plus-lg"></i> Add Employee
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                            <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mt-2 mt-sm-0">
                        <select class="form-select" id="perPage">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                        </select>
                    </div>
                </div>

                <div id="alertContainer"></div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Employee ID</th>
                                <th>Birth Date</th>
                                <th>Employee Name</th>
                                <th class="salary-col">Salary</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-4">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div id="paginationInfo" class="text-muted small"></div>
                    <nav>
                        <ul class="pagination" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalTitle">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="employeeForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrfToken); ?>">
                        <input type="hidden" name="recid" id="recid">

                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="employee_id" name="employee_id"
                                       placeholder="EMP-0001" required pattern="EMP-\d{4,}">
                                <button type="button" class="btn btn-outline-secondary" id="btnGenerateId" title="Generate ID">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </div>
                            <small class="text-muted">Format: EMP-XXXX (e.g., EMP-0001)</small>
                        </div>

                        <div class="mb-3">
                            <label for="employee_name" class="form-label">Employee Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="employee_name" name="employee_name"
                                   placeholder="Enter employee name" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="salary" class="form-label">Salary <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="salary" name="salary"
                                       placeholder="0.00" required min="0" step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date">
                        </div>

                        <div id="formAlertContainer"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveEmployee">
                            <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete employee <strong id="deleteEmployeeName"></strong>?</p>
                    <input type="hidden" id="deleteRecid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                        <span class="spinner-border spinner-border-sm d-none" id="deleteSpinner"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '<?php echo escapeOutput($csrfToken); ?>';
        let currentPage = 1;
        let searchTerm = '';

        const employeeModal = new bootstrap.Modal(document.getElementById('employeeModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        $(document).ready(function() {
            loadEmployees();

            // Add Employee button
            $('#btnAddEmployee').on('click', function() {
                resetForm();
                $('#employeeModalTitle').text('Add Employee');
                generateEmployeeId();
                $('#employee_id').prop('readonly', true);
                $('#btnGenerateId').hide();
                employeeModal.show();
            });

            // Generate ID button
            $('#btnGenerateId').on('click', generateEmployeeId);

            // Save Employee
            $('#employeeForm').on('submit', function(e) {
                e.preventDefault();
                saveEmployee();
            });

            // Confirm Delete
            $('#btnConfirmDelete').on('click', deleteEmployee);

            // Search
            $('#btnSearch').on('click', function() {
                searchTerm = $('#searchInput').val();
                currentPage = 1;
                loadEmployees();
            });

            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    searchTerm = $(this).val();
                    currentPage = 1;
                    loadEmployees();
                }
            });

            // Per page change
            $('#perPage').on('change', function() {
                currentPage = 1;
                loadEmployees();
            });
        });

        function loadEmployees() {
            const perPage = $('#perPage').val();

            $.ajax({
                url: 'index.php?action=employee.get.all',
                method: 'POST',
                data: {
                    page: currentPage,
                    per_page: perPage,
                    search: searchTerm
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderTable(response.employees, response.pagination);
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('Failed to load employees. Please try again.', 'danger');
                }
            });
        }

        function renderTable(employees, pagination) {
            const tbody = $('#employeeTableBody');
            tbody.empty();

            if (employees.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">No employees found.</td></tr>');
            } else {
                const startIndex = (pagination.current_page - 1) * pagination.per_page;
                employees.forEach((emp, index) => {
                    tbody.append(`
                        <tr>
                            <td>${startIndex + index + 1}</td>
                            <td>${emp.employee_id}</td>
                            <td>${emp.birth_date || ''}</td>
                            <td>${emp.employee_name}</td>
                            <td class="salary-col">$${emp.salary}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary btn-action" onclick="editEmployee(${emp.recid})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-action" onclick="showDeleteModal(${emp.recid}, '${emp.employee_name.replace(/'/g, "\\'")}')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }

            renderPagination(pagination);
        }

        function renderPagination(pagination) {
            const paginationEl = $('#pagination');
            const paginationInfo = $('#paginationInfo');
            paginationEl.empty();

            const start = (pagination.current_page - 1) * pagination.per_page + 1;
            const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_count);
            paginationInfo.text(`Showing ${pagination.total_count === 0 ? 0 : start}-${end} of ${pagination.total_count} entries`);

            if (pagination.total_pages <= 1) return;

            // Previous
            paginationEl.append(`
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="goToPage(${pagination.current_page - 1}); return false;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `);

            // Page numbers
            let startPage = Math.max(1, pagination.current_page - 2);
            let endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

            if (startPage > 1) {
                paginationEl.append('<li class="page-item"><a class="page-link" href="#" onclick="goToPage(1); return false;">1</a></li>');
                if (startPage > 2) {
                    paginationEl.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationEl.append(`
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>
                    </li>
                `);
            }

            if (endPage < pagination.total_pages) {
                if (endPage < pagination.total_pages - 1) {
                    paginationEl.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
                }
                paginationEl.append(`<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${pagination.total_pages}); return false;">${pagination.total_pages}</a></li>`);
            }

            // Next
            paginationEl.append(`
                <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="goToPage(${pagination.current_page + 1}); return false;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `);
        }

        function goToPage(page) {
            currentPage = page;
            loadEmployees();
        }

        function generateEmployeeId() {
            $.ajax({
                url: 'index.php?action=employee.generate.id',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#employee_id').val(response.employee_id);
                    }
                }
            });
        }

        function editEmployee(recid) {
            $.ajax({
                url: 'index.php?action=employee.get',
                method: 'POST',
                data: { recid: recid },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const emp = response.employee;
                        resetForm();
                        $('#employeeModalTitle').text('Edit Employee');
                        $('#recid').val(emp.recid);
                        $('#employee_id').val(emp.employee_id).prop('readonly', true);
                        $('#btnGenerateId').hide();
                        $('#employee_name').val(emp.employee_name);
                        $('#salary').val(emp.salary);
                        $('#birth_date').val(emp.birth_date);
                        employeeModal.show();
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('Failed to load employee data.', 'danger');
                }
            });
        }

        function saveEmployee() {
            const recid = $('#recid').val();
            const action = recid ? 'employee.edit' : 'employee.add';

            $('#saveSpinner').removeClass('d-none');
            $('#btnSaveEmployee').prop('disabled', true);

            $.ajax({
                url: `index.php?action=${action}`,
                method: 'POST',
                data: $('#employeeForm').serialize() + '&csrf_token=' + csrfToken,
                dataType: 'json',
                success: function(response) {
                    $('#saveSpinner').addClass('d-none');
                    $('#btnSaveEmployee').prop('disabled', false);

                    if (response.success) {
                        employeeModal.hide();
                        showAlert(response.message, 'success');
                        loadEmployees();
                    } else {
                        showFormAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    $('#saveSpinner').addClass('d-none');
                    $('#btnSaveEmployee').prop('disabled', false);
                    showFormAlert('Failed to save employee. Please try again.', 'danger');
                }
            });
        }

        function showDeleteModal(recid, name) {
            $('#deleteRecid').val(recid);
            $('#deleteEmployeeName').text(name);
            deleteModal.show();
        }

        function deleteEmployee() {
            const recid = $('#deleteRecid').val();

            $('#deleteSpinner').removeClass('d-none');
            $('#btnConfirmDelete').prop('disabled', true);

            $.ajax({
                url: 'index.php?action=employee.delete',
                method: 'POST',
                data: {
                    recid: recid,
                    csrf_token: csrfToken
                },
                dataType: 'json',
                success: function(response) {
                    $('#deleteSpinner').addClass('d-none');
                    $('#btnConfirmDelete').prop('disabled', false);

                    if (response.success) {
                        deleteModal.hide();
                        showAlert(response.message, 'success');
                        loadEmployees();
                    } else {
                        deleteModal.hide();
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    $('#deleteSpinner').addClass('d-none');
                    $('#btnConfirmDelete').prop('disabled', false);
                    deleteModal.hide();
                    showAlert('Failed to delete employee. Please try again.', 'danger');
                }
            });
        }

        function resetForm() {
            $('#employeeForm')[0].reset();
            $('#recid').val('');
            $('#formAlertContainer').empty();
            $('#employee_id').prop('readonly', false);
            $('#btnGenerateId').show();
        }

        function showAlert(message, type) {
            const alert = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#alertContainer').html(alert);

            setTimeout(() => {
                $('#alertContainer .alert').alert('close');
            }, 5000);
        }

        function showFormAlert(message, type) {
            const alert = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#formAlertContainer').html(alert);
        }
    </script>
</body>
</html>
