<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student Enrollment</h3>
                </div>
                <div class="card-body">
                    <!-- Enrollment Form -->
                    <div class="row mb-4">
                        <div class="col-md-10">
                            <h5>Submit New Enrollment Application</h5>
                            <form id="enrollmentForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="student_id" class="form-label">Student ID *</label>
                                        <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter your Student ID" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="course_program" class="form-label">Course Program *</label>
                                        <select class="form-select" id="course_program" name="course_program" required>
                                            <option value="">Select Course Program...</option>
                                            <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                                            <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                                            <option value="Bachelor of Science in Computer Engineering">Bachelor of Science in Computer Engineering</option>
                                            <option value="Bachelor of Science in Information Systems">Bachelor of Science in Information Systems</option>
                                            <option value="Associate in Computer Technology">Associate in Computer Technology</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="year_level" class="form-label">Year Level *</label>
                                        <select class="form-select" id="year_level" name="year_level" required>
                                            <option value="">Select Year Level...</option>
                                            <option value="1st Year">1st Year</option>
                                            <option value="2nd Year">2nd Year</option>
                                            <option value="3rd Year">3rd Year</option>
                                            <option value="4th Year">4th Year</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_number" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Address *</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                                    </div>
                                </div>
                                
                                <h6 class="mt-4 mb-3">Guardian Information (Optional)</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="guardian_name" class="form-label">Guardian Name</label>
                                        <input type="text" class="form-control" id="guardian_name" name="guardian_name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="guardian_phone" class="form-label">Guardian Phone</label>
                                        <input type="tel" class="form-control" id="guardian_phone" name="guardian_phone">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="guardian_email" class="form-label">Guardian Email</label>
                                        <input type="email" class="form-control" id="guardian_email" name="guardian_email">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Submit Enrollment Application</button>
                            </form>
                        </div>
                    </div>

                    <!-- My Enrollment Applications -->
                    <hr>
                    <h5>My Enrollment Applications</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student Number</th>
                                    <th>Course & Year</th>
                                    <th>Submitted Date</th>
                                    <th>Status</th>
                                    <th>Processed By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($enrollments)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No enrollment applications found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($enrollments as $enrollment): ?>
                                        <tr>
                                            <td><?= esc($enrollment['student_number'] ?? 'N/A') ?></td>
                                            <td><?= esc($enrollment['course_year'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php 
                                                $date = $enrollment['submitted_at'] ?? $enrollment['enrollment_date'] ?? date('Y-m-d H:i:s');
                                                echo date('M d, Y H:i', strtotime($date)); 
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $enrollment['status'] ?? 'pending';
                                                $statusClass = '';
                                                switch (strtolower($status)) {
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
                                                <span class="<?= $statusClass ?>"><?= ucfirst(strtolower($status)) ?></span>
                                            </td>
                                            <td><?= esc($enrollment['approved_by_name'] ?? '-') ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewEnrollment(<?= $enrollment['id'] ?>)">
                                                    View Details
                                                </button>
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
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle enrollment form submission
    $('#enrollmentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        var studentId = $('#student_id').val().trim();
        var fullName = $('#full_name').val().trim();
        var courseProgram = $('#course_program').val();
        var yearLevel = $('#year_level').val();
        var email = $('#email').val().trim();
        var phone = $('#phone_number').val().trim();
        var address = $('#address').val().trim();
        var dob = $('#date_of_birth').val();
        
        if (!studentId || !fullName || !courseProgram || !yearLevel || !email || !phone || !address || !dob) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // Show loading state
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
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                    console.log('Response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr.responseText);
                alert('An error occurred while submitting the enrollment. Please try again.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});

function viewEnrollment(id) {
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
                            <p><strong>Student ID:</strong> ${enrollment.student_number}</p>
                            <p><strong>Full Name:</strong> ${enrollment.full_name}</p>
                            <p><strong>Course Program & Year:</strong> ${enrollment.course_year}</p>
                            <p><strong>Email:</strong> ${enrollment.email}</p>
                            <p><strong>Phone:</strong> ${enrollment.phone_number}</p>
                            <p><strong>Date of Birth:</strong> ${new Date(enrollment.date_of_birth).toLocaleDateString()}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Application Status</h6>
                            <p><strong>Status:</strong> <span class="badge bg-${enrollment.status === 'approved' ? 'success' : enrollment.status === 'rejected' ? 'danger' : 'warning'}">${enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1)}</span></p>
                            <p><strong>Submitted Date:</strong> ${new Date(enrollment.submitted_at || enrollment.enrollment_date).toLocaleDateString()}</p>
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
</script>