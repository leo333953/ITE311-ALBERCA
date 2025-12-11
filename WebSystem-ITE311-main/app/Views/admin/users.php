<div class="d-flex" style="min-height: 100vh; background: #f4f6f9;">
    <!-- Sidebar Navigation -->
    <nav class="sidebar bg-dark text-white vh-100 p-3" style="width: 220px;">
        <h3 class="text-white mb-4">LMS Portal</h3>
        <ul class="nav flex-column mb-4">
            <li class="nav-item mb-2"><a href="<?= base_url('dashboard') ?>" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li class="nav-item mb-2"><a href="<?= base_url('users') ?>" class="nav-link text-white active"><i class="bi bi-people-fill me-2"></i>Manage Users</a></li>
            <li class="nav-item mb-2"><a href="<?= base_url('reports') ?>" class="nav-link text-white"><i class="bi bi-bar-chart-line me-2"></i>Analytics</a></li>
            <li class="nav-item mb-2"><a href="<?= base_url('courses') ?>" class="nav-link text-white"><i class="bi bi-journal-bookmark me-2"></i>Course Catalog</a></li>
            <li class="nav-item mb-2"><a href="<?= base_url('settings') ?>" class="nav-link text-white"><i class="bi bi-gear-fill me-2"></i>System Settings</a></li>
        </ul>

        <div class="mt-auto">
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
    </nav>




    <!-- Main Content -->
    <div class="flex-grow-1 p-4 ms-220">    
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-1">User Management</h2>
                <p class="text-muted mb-0">Manage all users in the system</p>
            </div>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle me-2"></i>Add New User
                </button>
            </div>
        </div>




        <!-- Quick Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card hover-card shadow-sm p-3">
                    <h5>Total Users</h5>
                    <h2><?= $totalUsers ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card hover-card shadow-sm p-3">
                    <h5>Admins</h5>
                    <h2><?= $adminCount ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card hover-card shadow-sm p-3">
                    <h5>Teachers</h5>
                    <h2><?= $teacherCount ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card hover-card shadow-sm p-3">
                    <h5>Students</h5>
                    <h2><?= $studentCount ?></h2>
                </div>
            </div>
        </div>




        <!-- Search and Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email...">
                    </div>
                    <div class="col-md-4">
                        <select id="roleFilter" class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card shadow-sm p-4">
            <h5>All Users</h5>
            <div class="table-responsive mt-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <?php foreach($users as $user): ?>
                        <tr data-role="<?= $user['role'] ?>" data-name="<?= strtolower($user['name']) ?>" data-email="<?= strtolower($user['email']) ?>">
                            <td><?= esc($user['name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <?php
                                    $badgeClass = match($user['role']) {
                                        'admin' => 'bg-danger',
                                        'teacher' => 'bg-success',
                                        'student' => 'bg-primary',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($user['role']) ?></span>
                            </td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editUser(<?= $user['id'] ?>)">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteUser(<?= $user['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="addName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="addName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="addEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="addEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="addPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="addPassword" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="addRole" class="form-label">Role</label>
                        <select class="form-select" id="addRole" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Create User</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current">
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateUser()">Update User</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Search functionality
$('#searchInput').on('keyup', function() {
    filterTable();
});

// Role filter
$('#roleFilter').on('change', function() {
    filterTable();
});

function filterTable() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const roleFilter = $('#roleFilter').val();

    $('#usersTableBody tr').each(function() {
        const name = $(this).data('name');
        const email = $(this).data('email');
        const role = $(this).data('role');

        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRole = !roleFilter || role === roleFilter;

        if (matchesSearch && matchesRole) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

// Save new user
function saveUser() {
    // Validation
    const name = $('#addName').val().trim();
    const email = $('#addEmail').val().trim();
    const password = $('#addPassword').val();
    const role = $('#addRole').val();

    if (!name) {
        alert('Please enter a name');
        $('#addName').focus();
        return;
    }

    if (!email) {
        alert('Please enter an email');
        $('#addEmail').focus();
        return;
    }

    if (!password) {
        alert('Please enter a password');
        $('#addPassword').focus();
        return;
    }

    if (password.length < 6) {
        alert('Password must be at least 6 characters');
        $('#addPassword').focus();
        return;
    }

    if (!role) {
        alert('Please select a role');
        $('#addRole').focus();
        return;
    }

    // Disable button to prevent double submission
    const $submitBtn = $('.btn-primary[onclick="saveUser()"]');
    const originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Creating...');

    const formData = {
        name: name,
        email: email,
        password: password,
        role: role
    };

    $.ajax({
        url: '<?= base_url('users/create') ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert('User created successfully!');
                $('#addUserModal').modal('hide');
                $('#addUserForm')[0].reset();
                location.reload();
            } else {
                // Show validation errors if available
                if (response.errors) {
                    let errorMsg = 'Validation errors:\n';
                    for (let field in response.errors) {
                        errorMsg += '- ' + response.errors[field] + '\n';
                    }
                    alert(errorMsg);
                } else {
                    alert(response.message || 'Failed to create user');
                }
                $submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText);
            alert('An error occurred. Please try again.\n' + (xhr.responseJSON?.message || error));
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

// Edit user
function editUser(id) {
    $.ajax({
        url: '<?= base_url('users/get') ?>/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#editUserId').val(response.user.id);
                $('#editName').val(response.user.name);
                $('#editEmail').val(response.user.email);
                $('#editRole').val(response.user.role);
                $('#editPassword').val('');
                $('#editUserModal').modal('show');
            }
        }
    });
}

// Update user
function updateUser() {
    const userId = $('#editUserId').val();
    const name = $('#editName').val().trim();
    const email = $('#editEmail').val().trim();
    const password = $('#editPassword').val();
    const role = $('#editRole').val();

    // Validation
    if (!name) {
        alert('Please enter a name');
        $('#editName').focus();
        return;
    }

    if (!email) {
        alert('Please enter an email');
        $('#editEmail').focus();
        return;
    }

    if (password && password.length < 6) {
        alert('Password must be at least 6 characters');
        $('#editPassword').focus();
        return;
    }

    if (!role) {
        alert('Please select a role');
        $('#editRole').focus();
        return;
    }

    // Disable button to prevent double submission
    const $submitBtn = $('.btn-primary[onclick="updateUser()"]');
    const originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

    const formData = {
        name: name,
        email: email,
        password: password,
        role: role
    };

    $.ajax({
        url: '<?= base_url('users/update') ?>/' + userId,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert('User updated successfully!');
                $('#editUserModal').modal('hide');
                location.reload();
            } else {
                if (response.errors) {
                    let errorMsg = 'Validation errors:\n';
                    for (let field in response.errors) {
                        errorMsg += '- ' + response.errors[field] + '\n';
                    }
                    alert(errorMsg);
                } else {
                    alert(response.message || 'Failed to update user');
                }
                $submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText);
            alert('An error occurred. Please try again.\n' + (xhr.responseJSON?.message || error));
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

// Delete user
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        $.ajax({
            url: '<?= base_url('users/delete') ?>/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('User deleted successfully!');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete user');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                alert('An error occurred. Please try again.\n' + (xhr.responseJSON?.message || error));
            }
        });
    }
}
</script>

<style>
.sidebar { position: fixed; top: 0; left: 0; display: flex; flex-direction: column; justify-content: space-between; height: 100vh; }
.ms-220 { margin-left: 220px; }
.hover-card { transition: all 0.25s ease-in-out; cursor: pointer; }
.hover-card:hover { transform: translateY(-6px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
.nav-link:hover { background-color: rgba(255,255,255,0.1); border-radius: 8px; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
</style>

