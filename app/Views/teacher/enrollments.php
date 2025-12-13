<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Enrollment Management</h3>
                </div>
                <div class="card-body">
                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h4><?= $stats['total'] ?></h4>
                                    <p>Total Enrollments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h4><?= $stats['pending'] ?></h4>
                                    <p>Pending Approval</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h4><?= $stats['approved'] ?></h4>
                                    <p>Approved</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h4><?= $stats['rejected'] ?></h4>
                                    <p>Rejected</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, or student ID...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="sortBy">
                                <option value="submitted_at">Sort by Date</option>
                                <option value="name">Sort by Name</option>
                                <option value="status">Sort by Status</option>
                                <option value="course">Sort by Course</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" onclick="searchEnrollments()">Search</button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary" onclick="clearSearch()">Clear</button>
                        </div>
                    </div>

                    <!-- Enrollments Table -->
                    <h5>All Enrollments</h5>
                    <div class="table-responsive">
                        <table class="table table-striped" id="enrollmentsTable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course & Year</th>
                                    <th>Submitted Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="enrollmentsTableBody">
                                <?php if (empty($pendingEnrollments)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No enrollments found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pendingEnrollments as $enrollment): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($enrollment['full_name']) ?></strong><br>
                                                <small class="text-muted"><?= esc($enrollment['email']) ?></small><br>
                                                <small class="text-muted">ID: <?= esc($enrollment['student_number']) ?></small>
                                            </td>
                                            <td><?= esc($enrollment['course_year']) ?></td>
                                            <td><?= date('M d, Y H:i', strtotime($enrollment['submitted_at'])) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = 'bg-warning';
                                                if ($enrollment['status'] === 'approved') $statusClass = 'bg-success';
                                                elseif ($enrollment['status'] === 'rejected') $statusClass = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= ucfirst($enrollment['status']) ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-info" onclick="viewEnrollment(<?= $enrollment['id'] ?>)">
                                                        View
                                                    </button>
                                                    <?php if ($enrollment['status'] === 'pending'): ?>
                                                        <button class="btn btn-sm btn-success" onclick="approveEnrollment(<?= $enrollment['id'] ?>)">
                                                            Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="rejectEnrollment(<?= $enrollment['id'] ?>)">
                                                            Reject
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteEnrollment(<?= $enrollment['id'] ?>)" title="Delete Enrollment">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Search Results Info -->
                    <div id="searchResults" class="mt-2" style="display: none;">
                        <small class="text-muted">Showing <span id="resultCount">0</span> results</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Details Modal -->
<div class="modal fade" id="enrollmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enrollment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="enrollmentDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approveBtn" onclick="approveFromModal()">Approve</button>
                <button type="button" class="btn btn-danger" id="rejectBtn" onclick="rejectFromModal()">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejection_reason" name="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Reject Enrollment</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEnrollmentId = null;

function viewEnrollment(id) {
    currentEnrollmentId = id;
    $.ajax({
        url: '<?= base_url('enrollments/get/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const enrollment = response.enrollment;
                const studentInfo = enrollment.student_info ? JSON.parse(enrollment.student_info) : {};
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <p><strong>Student Number:</strong> ${enrollment.student_number}</p>
                            <p><strong>Full Name:</strong> ${enrollment.full_name}</p>
                            <p><strong>Course & Year:</strong> ${enrollment.course_year}</p>
                            <p><strong>Email:</strong> ${enrollment.email}</p>
                            <p><strong>Phone:</strong> ${enrollment.phone_number}</p>
                            <p><strong>Date of Birth:</strong> ${new Date(enrollment.date_of_birth).toLocaleDateString()}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Application Information</h6>
                            <p><strong>Submitted Date:</strong> ${new Date(enrollment.submitted_at).toLocaleDateString()}</p>
                            <p><strong>Status:</strong> <span class="badge bg-warning">Pending</span></p>
                            <p><strong>Address:</strong> ${enrollment.address}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Guardian Information</h6>
                            <p><strong>Guardian Name:</strong> ${enrollment.guardian_name || 'N/A'}</p>
                            <p><strong>Guardian Phone:</strong> ${enrollment.guardian_phone || 'N/A'}</p>
                            <p><strong>Guardian Email:</strong> ${enrollment.guardian_email || 'N/A'}</p>
                        </div>
                    </div>
                `;
                
                $('#enrollmentDetails').html(html);
                
                // Show/hide action buttons based on status
                if (enrollment.status === 'pending') {
                    $('#approveBtn, #rejectBtn').show();
                } else {
                    $('#approveBtn, #rejectBtn').hide();
                }
                
                $('#enrollmentModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while loading enrollment details.');
        }
    });
}

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

function rejectEnrollment(id) {
    currentEnrollmentId = id;
    $('#rejectModal').modal('show');
}

function approveFromModal() {
    if (currentEnrollmentId) {
        $('#enrollmentModal').modal('hide');
        approveEnrollment(currentEnrollmentId);
    }
}

function rejectFromModal() {
    if (currentEnrollmentId) {
        $('#enrollmentModal').modal('hide');
        rejectEnrollment(currentEnrollmentId);
    }
}

function confirmReject() {
    const reason = $('#rejection_reason').val();
    if (!reason.trim()) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('enrollments/reject/') ?>' + currentEnrollmentId,
        type: 'POST',
        data: { reason: reason },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert(response.message);
                $('#rejectModal').modal('hide');
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

function deleteEnrollment(id) {
    if (confirm('Are you sure you want to delete this enrollment? This action will soft delete the record and can be recovered by an admin.')) {
        $.ajax({
            url: '<?= base_url('enrollments/delete/') ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Remove the row from the table immediately
                    $('button[onclick="deleteEnrollment(' + id + ')"]').closest('tr').fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty and show "no enrollments" message
                        if ($('#enrollmentsTableBody tr:visible').length === 0) {
                            $('#enrollmentsTableBody').html('<tr><td colspan="5" class="text-center">No enrollments found</td></tr>');
                        }
                        
                        // Update search results count if search is active
                        if ($('#searchResults').is(':visible')) {
                            const currentCount = parseInt($('#resultCount').text()) - 1;
                            $('#resultCount').text(Math.max(0, currentCount));
                        }
                    });
                    
                    // Show success message
                    alert(response.message);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the enrollment.');
            }
        });
    }
}

function searchEnrollments() {
    const searchTerm = $('#searchInput').val();
    const statusFilter = $('#statusFilter').val();
    const sortBy = $('#sortBy').val();
    
    $.ajax({
        url: '<?= base_url('enrollments/search') ?>',
        type: 'GET',
        data: {
            search: searchTerm,
            status: statusFilter,
            sort_by: sortBy
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                updateEnrollmentsTable(response.enrollments);
                $('#searchResults').show();
                $('#resultCount').text(response.count);
            } else {
                alert('Search failed: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred during search.');
        }
    });
}

function clearSearch() {
    $('#searchInput').val('');
    $('#statusFilter').val('');
    $('#sortBy').val('submitted_at');
    $('#searchResults').hide();
    location.reload();
}

function updateEnrollmentsTable(enrollments) {
    let html = '';
    
    if (enrollments.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No enrollments found</td></tr>';
    } else {
        enrollments.forEach(function(enrollment) {
            let statusClass = 'bg-warning';
            if (enrollment.status === 'approved') statusClass = 'bg-success';
            else if (enrollment.status === 'rejected') statusClass = 'bg-danger';
            
            let actionButtons = `
                <button class="btn btn-sm btn-info" onclick="viewEnrollment(${enrollment.id})">View</button>
            `;
            
            if (enrollment.status === 'pending') {
                actionButtons += `
                    <button class="btn btn-sm btn-success" onclick="approveEnrollment(${enrollment.id})">Approve</button>
                    <button class="btn btn-sm btn-danger" onclick="rejectEnrollment(${enrollment.id})">Reject</button>
                `;
            }
            
            actionButtons += `
                <button class="btn btn-sm btn-outline-danger" onclick="deleteEnrollment(${enrollment.id})" title="Delete Enrollment">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            const submittedDate = new Date(enrollment.submitted_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            html += `
                <tr>
                    <td>
                        <strong>${enrollment.full_name}</strong><br>
                        <small class="text-muted">${enrollment.email}</small><br>
                        <small class="text-muted">ID: ${enrollment.student_number}</small>
                    </td>
                    <td>${enrollment.course_year}</td>
                    <td>${submittedDate}</td>
                    <td><span class="badge ${statusClass}">${enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1)}</span></td>
                    <td><div class="btn-group" role="group">${actionButtons}</div></td>
                </tr>
            `;
        });
    }
    
    $('#enrollmentsTableBody').html(html);
}

// Auto-search on input
$(document).ready(function() {
    $('#searchInput').on('keyup', function(e) {
        if (e.key === 'Enter') {
            searchEnrollments();
        }
    });
    
    $('#statusFilter, #sortBy').on('change', function() {
        if ($('#searchInput').val() || $('#statusFilter').val()) {
            searchEnrollments();
        }
    });
});
</script>