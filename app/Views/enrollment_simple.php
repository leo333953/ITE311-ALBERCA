<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Enrollment System - <?= ucfirst($role ?? 'Guest') ?></h1>
        
        <?php if (($role ?? '') === 'student'): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3>Submit Enrollment Application</h3>
                        </div>
                        <div class="card-body">
                            <form id="enrollmentForm">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="course_year" class="form-label">Course & Year *</label>
                                    <select class="form-select" id="course_year" name="course_year" required>
                                        <option value="">Select Course & Year...</option>
                                        <option value="BSIT 1st Year">BSIT 1st Year</option>
                                        <option value="BSIT 2nd Year">BSIT 2nd Year</option>
                                        <option value="BSIT 3rd Year">BSIT 3rd Year</option>
                                        <option value="BSIT 4th Year">BSIT 4th Year</option>
                                        <option value="BSCS 1st Year">BSCS 1st Year</option>
                                        <option value="BSCS 2nd Year">BSCS 2nd Year</option>
                                        <option value="BSCS 3rd Year">BSCS 3rd Year</option>
                                        <option value="BSCS 4th Year">BSCS 4th Year</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address *</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>My Applications</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($enrollments ?? [])): ?>
                                <p class="text-muted">No applications submitted yet.</p>
                            <?php else: ?>
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <div class="border-bottom pb-2 mb-2">
                                        <strong><?= esc($enrollment['course_year'] ?? 'N/A') ?></strong><br>
                                        <small class="text-muted">
                                            Status: 
                                            <span class="badge bg-<?= ($enrollment['status'] ?? 'pending') === 'approved' ? 'success' : (($enrollment['status'] ?? 'pending') === 'rejected' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($enrollment['status'] ?? 'pending') ?>
                                            </span>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php elseif (($role ?? '') === 'teacher'): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Pending Enrollments</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingEnrollments ?? [])): ?>
                        <p class="text-muted">No pending enrollments.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingEnrollments as $enrollment): ?>
                                        <tr>
                                            <td><?= esc($enrollment['full_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($enrollment['course_year'] ?? 'N/A') ?></td>
                                            <td><?= date('M d, Y', strtotime($enrollment['submitted_at'] ?? $enrollment['enrollment_date'] ?? 'now')) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success" onclick="approveEnrollment(<?= $enrollment['id'] ?>)">Approve</button>
                                                <button class="btn btn-sm btn-danger" onclick="rejectEnrollment(<?= $enrollment['id'] ?>)">Reject</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        <?php elseif (($role ?? '') === 'admin'): ?>
            <div class="card">
                <div class="card-header">
                    <h3>All Enrollments</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($allEnrollments ?? [])): ?>
                        <p class="text-muted">No enrollments found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allEnrollments as $enrollment): ?>
                                        <tr>
                                            <td><?= esc($enrollment['full_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($enrollment['course_year'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($enrollment['status'] ?? 'pending') === 'approved' ? 'success' : (($enrollment['status'] ?? 'pending') === 'rejected' ? 'danger' : 'warning') ?>">
                                                    <?= ucfirst($enrollment['status'] ?? 'pending') ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($enrollment['submitted_at'] ?? $enrollment['enrollment_date'] ?? 'now')) ?></td>
                                            <td>
                                                <?php if (($enrollment['status'] ?? 'pending') === 'pending'): ?>
                                                    <button class="btn btn-sm btn-success" onclick="approveEnrollment(<?= $enrollment['id'] ?>)">Approve</button>
                                                    <button class="btn btn-sm btn-danger" onclick="rejectEnrollment(<?= $enrollment['id'] ?>)">Reject</button>
                                                <?php else: ?>
                                                    <span class="text-muted">Processed</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        <?php else: ?>
            <div class="alert alert-warning">
                <h4>Access Denied</h4>
                <p>Please <a href="<?= base_url('login') ?>">login</a> to access the enrollment system.</p>
            </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
            <a href="<?= base_url('test-enrollment') ?>" class="btn btn-info">Test System</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Handle enrollment form submission
        $('#enrollmentForm').on('submit', function(e) {
            e.preventDefault();
            
            var submitBtn = $(this).find('button[type="submit"]');
            var originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Submitting...');
            
            $.ajax({
                url: '<?= base_url('enrollments/submit') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Approve enrollment
        function approveEnrollment(id) {
            if (confirm('Are you sure you want to approve this enrollment?')) {
                $.ajax({
                    url: '<?= base_url('enrollments/approve/') ?>' + id,
                    type: 'POST',
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
                        alert('An error occurred while approving the enrollment.');
                    }
                });
            }
        }
        
        // Reject enrollment
        function rejectEnrollment(id) {
            var reason = prompt('Please provide a reason for rejection:');
            if (reason) {
                $.ajax({
                    url: '<?= base_url('enrollments/reject/') ?>' + id,
                    type: 'POST',
                    data: { reason: reason },
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
                        alert('An error occurred while rejecting the enrollment.');
                    }
                });
            }
        }
    </script>
</body>
</html>