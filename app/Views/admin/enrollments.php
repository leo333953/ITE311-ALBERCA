<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Enrollment Administration</h3>
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

                    <!-- Enhanced Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by student name, email, student number, or course...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="bi bi-x-circle"></i> Clear
                                </button>
                                <button class="btn btn-primary" type="button" id="serverSearchBtn">
                                    <i class="bi bi-search"></i> Server Search
                                </button>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle"></i> Type to filter instantly, or click "Server Search" for database search
                            </small>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <select id="statusFilter" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <select id="sortBy" class="form-select">
                                    <option value="submitted_at">Sort by Date</option>
                                    <option value="name">Sort by Name</option>
                                    <option value="status">Sort by Status</option>
                                    <option value="course">Sort by Course</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results Info -->
                    <div id="searchInfo" class="alert alert-info d-none">
                        <i class="bi bi-info-circle"></i> <span id="searchInfoText"></span>
                    </div>

                    <!-- All Enrollments -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="enrollmentsTable">
                            <thead>
                                <tr>
                                    <th>Student Number</th>
                                    <th>Student</th>
                                    <th>Course & Year</th>
                                    <th>Submitted Date</th>
                                    <th>Status</th>
                                    <th>Processed By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($allEnrollments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No enrollments found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($allEnrollments as $enrollment): ?>
                                        <tr data-status="<?= strtolower($enrollment['status']) ?>" data-course="<?= substr($enrollment['course_year'], 0, 4) ?>" data-student="<?= strtolower($enrollment['full_name'] . ' ' . $enrollment['email']) ?>">
                                            <td><?= $enrollment['student_number'] ?></td>
                                            <td>
                                                <strong><?= esc($enrollment['full_name']) ?></strong><br>
                                                <small class="text-muted"><?= esc($enrollment['email']) ?></small>
                                            </td>
                                            <td><?= esc($enrollment['course_year']) ?></td>
                                            <td><?= date('M d, Y H:i', strtotime($enrollment['submitted_at'])) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($enrollment['status']) {
                                                    case 'pending':
                                                        $statusClass = 'badge bg-warning';
                                                        break;
                                                    case 'approved':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'badge bg-danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>"><?= ucfirst(strtolower($enrollment['status'])) ?></span>
                                            </td>
                                            <td>
                                                <?= $enrollment['approved_by_name'] ?? '-' ?><br>
                                                <?php if ($enrollment['processed_at']): ?>
                                                    <small class="text-muted"><?= date('M d, Y', strtotime($enrollment['processed_at'])) ?></small>
                                                <?php endif; ?>
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
let originalTableData = [];
let isServerSearchActive = false;

$(document).ready(function() {
    // Store original table data for client-side filtering
    storeOriginalTableData();
    
    // Set up event listeners
    setupSearchAndFilters();
});

function storeOriginalTableData() {
    originalTableData = [];
    $('#enrollmentsTable tbody tr').each(function() {
        if (!$(this).find('td').first().text().includes('No enrollments found')) {
            const row = {
                element: $(this).clone(),
                studentNumber: $(this).find('td:eq(0)').text().trim().toLowerCase(),
                studentName: $(this).find('td:eq(1) strong').text().trim().toLowerCase(),
                email: $(this).find('td:eq(1) small').text().trim().toLowerCase(),
                course: $(this).find('td:eq(2)').text().trim().toLowerCase(),
                date: $(this).find('td:eq(3)').text().trim(),
                status: $(this).data('status') || $(this).find('td:eq(4)').text().trim().toLowerCase(),
                processedBy: $(this).find('td:eq(5)').text().trim().toLowerCase()
            };
            originalTableData.push(row);
        }
    });
}

function setupSearchAndFilters() {
    // Real-time client-side search
    $('#searchInput').on('input', function() {
        if (!isServerSearchActive) {
            performClientSideFilter();
        }
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        if (!isServerSearchActive) {
            performClientSideFilter();
        }
    });

    // Sort functionality
    $('#sortBy').on('change', function() {
        if (!isServerSearchActive) {
            performClientSideFilter();
        }
    });

    // Server search button
    $('#serverSearchBtn').on('click', function() {
        performServerSearch();
    });

    // Clear search
    $('#clearSearch').on('click', function() {
        clearAllFilters();
    });

    // Enter key for server search
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            performServerSearch();
        }
    });
}

function performClientSideFilter() {
    const searchTerm = $('#searchInput').val().toLowerCase().trim();
    const statusFilter = $('#statusFilter').val();
    const sortBy = $('#sortBy').val();
    
    let filteredData = [...originalTableData];
    
    // Apply search filter
    if (searchTerm) {
        filteredData = filteredData.filter(row => 
            row.studentNumber.includes(searchTerm) || 
            row.studentName.includes(searchTerm) || 
            row.email.includes(searchTerm) || 
            row.course.includes(searchTerm)
        );
    }
    
    // Apply status filter
    if (statusFilter) {
        filteredData = filteredData.filter(row => row.status === statusFilter);
    }
    
    // Apply sorting
    filteredData.sort((a, b) => {
        switch (sortBy) {
            case 'name':
                return a.studentName.localeCompare(b.studentName);
            case 'status':
                return a.status.localeCompare(b.status);
            case 'course':
                return a.course.localeCompare(b.course);
            case 'submitted_at':
            default:
                return new Date(b.date) - new Date(a.date);
        }
    });
    
    // Update table
    updateTable(filteredData, searchTerm, false);
}

function performServerSearch() {
    const searchTerm = $('#searchInput').val().trim();
    const statusFilter = $('#statusFilter').val();
    const sortBy = $('#sortBy').val();
    
    if (!searchTerm && !statusFilter) {
        alert('Please enter a search term or select a status filter');
        return;
    }
    
    isServerSearchActive = true;
    showLoading();
    
    $.ajax({
        url: '<?= base_url('enrollments/search') ?>',
        method: 'GET',
        data: {
            search: searchTerm,
            status: statusFilter,
            sort_by: sortBy
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                renderServerResults(response.enrollments, searchTerm);
            } else {
                showError('Server search failed: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Server search error:', error);
            showError('Server search failed. Please try again.');
            isServerSearchActive = false;
        }
    });
}

function renderServerResults(enrollments, searchTerm) {
    const tbody = $('#enrollmentsTable tbody');
    tbody.empty();
    
    if (enrollments.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="bi bi-search"></i> No enrollments found matching your search criteria
                </td>
            </tr>
        `);
    } else {
        enrollments.forEach(enrollment => {
            const statusClass = getStatusClass(enrollment.status);
            const row = `
                <tr>
                    <td>${enrollment.student_number || 'N/A'}</td>
                    <td>
                        <strong>${escapeHtml(enrollment.full_name)}</strong><br>
                        <small class="text-muted">${escapeHtml(enrollment.email)}</small>
                    </td>
                    <td>${escapeHtml(enrollment.course_year)}</td>
                    <td>${new Date(enrollment.submitted_at).toLocaleDateString()}</td>
                    <td><span class="${statusClass}">${enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1)}</span></td>
                    <td>${enrollment.approved_by_name || '-'}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info" onclick="viewEnrollment(${enrollment.id})">
                                <i class="bi bi-eye"></i> View
                            </button>
                            ${enrollment.status === 'pending' ? `
                                <button class="btn btn-sm btn-success" onclick="approveEnrollment(${enrollment.id})">
                                    <i class="bi bi-check"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="rejectEnrollment(${enrollment.id})">
                                    <i class="bi bi-x"></i> Reject
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Show search info
    showSearchInfo(`Found ${enrollments.length} enrollment(s) from server search${searchTerm ? ' for "' + escapeHtml(searchTerm) + '"' : ''}`);
}

function updateTable(filteredData, searchTerm, isServerResult) {
    const tbody = $('#enrollmentsTable tbody');
    tbody.empty();
    
    if (filteredData.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="bi bi-search"></i> No enrollments found${searchTerm ? ' matching "' + escapeHtml(searchTerm) + '"' : ''}
                </td>
            </tr>
        `);
        showSearchInfo(`No enrollments found${searchTerm ? ' matching "' + escapeHtml(searchTerm) + '"' : ''}`);
    } else {
        filteredData.forEach(row => {
            tbody.append(row.element);
        });
        
        const filterInfo = [];
        if (searchTerm) filterInfo.push(`search: "${escapeHtml(searchTerm)}"`);
        if ($('#statusFilter').val()) filterInfo.push(`status: ${$('#statusFilter option:selected').text()}`);
        
        showSearchInfo(`Showing ${filteredData.length} of ${originalTableData.length} enrollments${filterInfo.length ? ' (' + filterInfo.join(', ') + ')' : ''}`);
    }
}

function clearAllFilters() {
    $('#searchInput').val('');
    $('#statusFilter').val('');
    $('#sortBy').val('submitted_at');
    isServerSearchActive = false;
    
    // Restore original table
    const tbody = $('#enrollmentsTable tbody');
    tbody.empty();
    originalTableData.forEach(row => {
        tbody.append(row.element);
    });
    
    hideSearchInfo();
}

function showLoading() {
    const tbody = $('#enrollmentsTable tbody');
    tbody.html(`
        <tr>
            <td colspan="7" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Searching database...</p>
            </td>
        </tr>
    `);
}

function showError(message) {
    const tbody = $('#enrollmentsTable tbody');
    tbody.html(`
        <tr>
            <td colspan="7" class="text-center text-danger py-4">
                <i class="bi bi-exclamation-triangle"></i> ${escapeHtml(message)}
                <br><button class="btn btn-sm btn-primary mt-2" onclick="clearAllFilters()">Reset</button>
            </td>
        </tr>
    `);
}

function showSearchInfo(text) {
    $('#searchInfoText').text(text);
    $('#searchInfo').removeClass('d-none');
}

function hideSearchInfo() {
    $('#searchInfo').addClass('d-none');
}

function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'pending': return 'badge bg-warning';
        case 'approved': return 'badge bg-success';
        case 'rejected': return 'badge bg-danger';
        default: return 'badge bg-secondary';
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

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
                            <h6>Application Status</h6>
                            <p><strong>Status:</strong> <span class="badge bg-${enrollment.status === 'approved' ? 'success' : enrollment.status === 'rejected' ? 'danger' : 'warning'}">${enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1)}</span></p>
                            <p><strong>Submitted Date:</strong> ${new Date(enrollment.submitted_at).toLocaleDateString()}</p>
                            ${enrollment.approved_by_name ? `<p><strong>Processed By:</strong> ${enrollment.approved_by_name}</p>` : ''}
                            ${enrollment.processed_at ? `<p><strong>Processed Date:</strong> ${new Date(enrollment.processed_at).toLocaleDateString()}</p>` : ''}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Contact Information</h6>
                            <p><strong>Address:</strong> ${enrollment.address}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Guardian Information</h6>
                            <p><strong>Guardian Name:</strong> ${enrollment.guardian_name || 'N/A'}</p>
                            <p><strong>Guardian Phone:</strong> ${enrollment.guardian_phone || 'N/A'}</p>
                            <p><strong>Guardian Email:</strong> ${enrollment.guardian_email || 'N/A'}</p>
                        </div>
                    </div>
                    ${enrollment.remarks ? `<div class="alert alert-${enrollment.status === 'approved' ? 'success' : 'danger'}"><strong>Remarks:</strong> ${enrollment.remarks}</div>` : ''}
                    ${enrollment.rejection_reason ? `<div class="alert alert-danger"><strong>Rejection Reason:</strong> ${enrollment.rejection_reason}</div>` : ''}
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
    if (confirm('Are you sure you want to delete this enrollment? This action will soft delete the record and can be recovered from the Soft Deletes section.')) {
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
                        if ($('#enrollmentsTable tbody tr:visible').length === 0) {
                            $('#enrollmentsTable tbody').html('<tr><td colspan="7" class="text-center">No enrollments found</td></tr>');
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
</script>