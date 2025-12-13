<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Soft Delete Management</h3>
                    <p class="text-muted mb-0">Manage soft-deleted records - restore or permanently delete them</p>
                </div>
                <div class="card-body">
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" id="softDeleteTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">
                                <i class="bi bi-journal"></i> Courses (<?= count($deletedCourses) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                                <i class="bi bi-people"></i> Users (<?= count($deletedUsers) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                                <i class="bi bi-file-earmark"></i> Materials (<?= count($deletedMaterials) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="enrollments-tab" data-bs-toggle="tab" data-bs-target="#enrollments" type="button" role="tab">
                                <i class="bi bi-clipboard-check"></i> Enrollments (<?= count($deletedEnrollments) ?>)
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="softDeleteTabContent">
                        <!-- Courses Tab -->
                        <div class="tab-pane fade show active" id="courses" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Deleted Courses</h5>
                                <?php if (!empty($deletedCourses)): ?>
                                    <button class="btn btn-success btn-sm" onclick="bulkRestore('courses')">
                                        <i class="bi bi-arrow-clockwise"></i> Restore All Courses
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (empty($deletedCourses)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No deleted courses found.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Deleted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deletedCourses as $course): ?>
                                                <tr>
                                                    <td><?= $course['id'] ?></td>
                                                    <td><strong><?= esc($course['title']) ?></strong></td>
                                                    <td><?= esc(substr($course['description'], 0, 100)) ?>...</td>
                                                    <td><?= date('M d, Y H:i', strtotime($course['deleted_at'])) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" onclick="restoreRecord('course', <?= $course['id'] ?>)">
                                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="permanentDelete('course', <?= $course['id'] ?>)">
                                                            <i class="bi bi-trash"></i> Permanent Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Users Tab -->
                        <div class="tab-pane fade" id="users" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Deleted Users</h5>
                                <?php if (!empty($deletedUsers)): ?>
                                    <button class="btn btn-success btn-sm" onclick="bulkRestore('users')">
                                        <i class="bi bi-arrow-clockwise"></i> Restore All Users
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (empty($deletedUsers)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No deleted users found.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Deleted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deletedUsers as $user): ?>
                                                <tr>
                                                    <td><?= $user['id'] ?></td>
                                                    <td><strong><?= esc($user['name']) ?></strong></td>
                                                    <td><?= esc($user['email']) ?></td>
                                                    <td><span class="badge bg-secondary"><?= ucfirst($user['role']) ?></span></td>
                                                    <td><?= date('M d, Y H:i', strtotime($user['deleted_at'])) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" onclick="restoreRecord('user', <?= $user['id'] ?>)">
                                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="permanentDelete('user', <?= $user['id'] ?>)">
                                                            <i class="bi bi-trash"></i> Permanent Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Materials Tab -->
                        <div class="tab-pane fade" id="materials" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Deleted Materials</h5>
                                <?php if (!empty($deletedMaterials)): ?>
                                    <button class="btn btn-success btn-sm" onclick="bulkRestore('materials')">
                                        <i class="bi bi-arrow-clockwise"></i> Restore All Materials
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (empty($deletedMaterials)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No deleted materials found.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>File Name</th>
                                                <th>Course ID</th>
                                                <th>Deleted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deletedMaterials as $material): ?>
                                                <tr>
                                                    <td><?= $material['id'] ?></td>
                                                    <td><strong><?= esc($material['file_name']) ?></strong></td>
                                                    <td><?= $material['course_id'] ?></td>
                                                    <td><?= date('M d, Y H:i', strtotime($material['deleted_at'])) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" onclick="restoreRecord('material', <?= $material['id'] ?>)">
                                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="permanentDelete('material', <?= $material['id'] ?>)">
                                                            <i class="bi bi-trash"></i> Permanent Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Enrollments Tab -->
                        <div class="tab-pane fade" id="enrollments" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Deleted Enrollments</h5>
                                <?php if (!empty($deletedEnrollments)): ?>
                                    <button class="btn btn-success btn-sm" onclick="bulkRestore('enrollments')">
                                        <i class="bi bi-arrow-clockwise"></i> Restore All Enrollments
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (empty($deletedEnrollments)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No deleted enrollments found.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Student</th>
                                                <th>Course</th>
                                                <th>Status</th>
                                                <th>Deleted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deletedEnrollments as $enrollment): ?>
                                                <tr>
                                                    <td><?= $enrollment['id'] ?></td>
                                                    <td><strong><?= esc($enrollment['full_name'] ?? 'N/A') ?></strong></td>
                                                    <td><?= esc($enrollment['course_year'] ?? 'N/A') ?></td>
                                                    <td><span class="badge bg-info"><?= ucfirst($enrollment['status']) ?></span></td>
                                                    <td><?= date('M d, Y H:i', strtotime($enrollment['deleted_at'])) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" onclick="restoreRecord('enrollment', <?= $enrollment['id'] ?>)">
                                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="permanentDelete('enrollment', <?= $enrollment['id'] ?>)">
                                                            <i class="bi bi-trash"></i> Permanent Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function restoreRecord(type, id) {
    if (confirm(`Are you sure you want to restore this ${type}?`)) {
        $.ajax({
            url: '<?= base_url('soft-deletes/restore') ?>',
            type: 'POST',
            data: {
                type: type,
                id: id,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while restoring the record.');
            }
        });
    }
}

function permanentDelete(type, id) {
    if (confirm(`Are you sure you want to PERMANENTLY delete this ${type}? This action cannot be undone!`)) {
        $.ajax({
            url: '<?= base_url('soft-deletes/permanent-delete') ?>',
            type: 'POST',
            data: {
                type: type,
                id: id,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while permanently deleting the record.');
            }
        });
    }
}

function bulkRestore(type) {
    if (confirm(`Are you sure you want to restore all deleted ${type}?`)) {
        $.ajax({
            url: '<?= base_url('soft-deletes/bulk-restore') ?>',
            type: 'POST',
            data: {
                type: type,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while restoring records.');
            }
        });
    }
}
</script>