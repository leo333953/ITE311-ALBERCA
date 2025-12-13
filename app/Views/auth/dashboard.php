<?= $this->include('templates/header') ?>

<div class="d-flex" style="min-height: 100vh; background: #f4f6f9;">
    <!-- Sidebar Navigation -->
    <nav class="sidebar bg-dark text-white vh-100 p-3" style="width: 220px;">
        <h3 class="text-white mb-4">LMS Portal</h3>
        <ul class="nav flex-column mb-4">
            <?php if($user_role === 'admin'): ?>
                <li class="nav-item mb-2"><a href="<?= base_url('dashboard') ?>" class="nav-link text-white active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('users') ?>" class="nav-link text-white"><i class="bi bi-people-fill me-2"></i>Manage Users</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('courses') ?>" class="nav-link text-white"><i class="bi bi-journal-bookmark me-2"></i>Course Catalog</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('reports') ?>" class="nav-link text-white"><i class="bi bi-bar-chart-line me-2"></i>Analytics</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('settings') ?>" class="nav-link text-white"><i class="bi bi-gear-fill me-2"></i>System Settings</a></li>

            <?php elseif($user_role === 'teacher'): ?>
                <li class="nav-item mb-2"><a href="<?= base_url('dashboard') ?>" class="nav-link text-white active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('courses/manage') ?>" class="nav-link text-white"><i class="bi bi-mortarboard me-2"></i>Manage Courses</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('students') ?>" class="nav-link text-white"><i class="bi bi-people me-2"></i>Students</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('lessons') ?>" class="nav-link text-white"><i class="bi bi-upload me-2"></i>Upload Lessons</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('announcements') ?>" class="nav-link text-white"><i class="bi bi-chat-left-text me-2"></i>Announcements</a></li>

            <?php elseif($user_role === 'student'): ?>
                <li class="nav-item mb-2"><a href="<?= base_url('dashboard') ?>" class="nav-link text-white active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('courses') ?>" class="nav-link text-white"><i class="bi bi-book me-2"></i>My Courses</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('assignments') ?>" class="nav-link text-white"><i class="bi bi-file-earmark-text me-2"></i>Assignments</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('groups') ?>" class="nav-link text-white"><i class="bi bi-people me-2"></i>Study Groups</a></li>
                <li class="nav-item mb-2"><a href="<?= base_url('progress') ?>" class="nav-link text-white"><i class="bi bi-trophy me-2"></i>Progress</a></li>
            <?php endif; ?>
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
                <h2 class="fw-bold text-primary mb-1"><?= ucfirst($user_role) ?> Dashboard</h2>
                <p class="text-muted mb-0">Welcome back, <span class="fw-semibold"><?= esc($user_name) ?></span> 👋</p>
            </div>
            <div>
                <button class="btn btn-outline-primary btn-sm me-2"><i class="bi bi-bell"></i> Notifications</button>
                <button class="btn btn-primary btn-sm"><i class="bi bi-person-circle"></i> My Profile</button>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="row g-4 mb-5">
            <?php if($user_role === 'student'): ?>
                <div class="col-md-3">
                    <div class="card hover-card shadow-sm p-3">
                        <h5>Enrolled Courses</h5>
                        <h2><?= count($enrolledCourses) ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card hover-card shadow-sm p-3">
                        <h5>Assignments</h5>
                        <h2>5</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card hover-card shadow-sm p-3">
                        <h5>Study Groups</h5>
                        <h2>2</h2>
                    </div>
                </div>

            <?php elseif($user_role === 'teacher'): ?>
                <div class="col-md-3">
                    <div class="card hover-card shadow-sm p-3">
                        <h5>Courses Managed</h5>
                        <h2><?= count($courses) ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card hover-card shadow-sm p-3">
                        <h5>Students</h5>
                        <h2>120</h2>
                    </div>
                </div>

            <?php elseif($user_role === 'admin'): ?>
                <div class="col-md-3">
                    <div class="card hover-card shadow-sm p-3">
                        <h5>Total Users</h5>
                        <h2>500</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card hover-card shadow-sm p-3">
                        <h5>Active Courses</h5>
                        <h2><?= count($courses) ?></h2>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Courses List -->
        <div class="card shadow-sm p-4">
            <h5>Courses</h5>
            <ul class="list-group mt-3">
                <?php foreach($courses as $course): 
                    $enrolled = false;
                    foreach($enrolledCourses as $ec) {
                        if ($ec['id'] == $course['id']) { $enrolled = true; break; }
                    }
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= esc($course['title']) ?>
                    <?php if($user_role === 'student'): ?>
                        <?php if($enrolled): ?>
                            <span class="badge bg-success rounded-pill enrolled-badge">Enrolled</span>
                        <?php else: ?>
                            <button class="btn btn-success btn-sm enroll-btn" data-course="<?= $course['id'] ?>" onclick="directEnroll(<?= $course['id'] ?>, '<?= esc($course['title']) ?>')">
                                <i class="bi bi-plus-circle"></i> Enroll
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if($user_role === 'teacher'): ?>
                            <a href="<?= base_url('admin/course/' . $course['id']. '/upload') ?>" class="btn btn-secondary btn-sm">Upload</a>
                        <?php else: ?>
                            <span class="badge bg-primary rounded-pill">View</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if($user_role === 'student'): ?>
        <div class="row g-4 mb-5 mt-4">
            <div class="col-12">
                <div class="card shadow-sm materials-section">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-download me-2"></i>My Course Materials
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($enrolledCourses)): ?>
                            <?php 
                            $hasMaterials = false;
                            foreach($enrolledCourses as $course): 
                                if (!empty($course['materials'])): 
                                    $hasMaterials = true;
                            ?>
                                <div class="mb-4">
                                    <h6 class="course-title">
                                        <i class="bi bi-book me-2"></i><?= esc($course['title']) ?>
                                    </h6>
                                    
                                    <?php foreach($course['materials'] as $material): ?>
                                        <div class="course-materials-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-pdf me-3 text-danger" style="font-size: 1.2rem;"></i>
                                                <span class="fw-medium"><?= esc($material['file_name']) ?></span>
                                            </div>
                                            
                                            <a href="<?= base_url('materials/download/'.$material['id']) ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="bi bi-download me-1"></i> Download
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php 
                                endif; 
                            endforeach; 
                            ?>
                            
                            <?php if(!$hasMaterials): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-folder-x text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mt-3 mb-0">No materials available for your enrolled courses.</p>
                                    <small class="text-muted">Materials will appear here once your instructors upload them.</small>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-book text-muted" style="font-size: 2.5rem;"></i>
                                <p class="text-muted mt-3 mb-2">No enrolled courses yet.</p>
                                <small class="text-muted">Enroll in courses above to access course materials.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Styles -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function directEnroll(courseId, courseName) {
    const button = $(`button[onclick*="directEnroll(${courseId}"]`);
    
    // Add loading state first
    button.html('<i class="bi bi-hourglass-split"></i> Enrolling...')
          .prop('disabled', true);
    
    // After a short delay, transform to enrolled state
    setTimeout(() => {
        button.removeClass('btn-success')
              .addClass('btn-success enrolled')
              .html('Enrolled')
              .css({
                  'background-color': '#28a745',
                  'border-color': '#28a745'
              });
        
        // Show success message
        alert('Enrolled successfully!');
    }, 300);
    
    // Send enrollment to backend
    $.ajax({
        url: '<?= base_url('course/enroll') ?>',
        method: 'POST',
        data: {
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            console.log('Enrollment response:', response);
            if (response.status === 'success') {
                console.log('Enrollment saved to database successfully');
            } else {
                // Server returned error in success callback
                console.error('Server error:', response.message);
                button.removeClass('btn-success enrolled')
                      .addClass('btn-success')
                      .html('<i class="bi bi-plus-circle"></i> Enroll')
                      .prop('disabled', false)
                      .css({
                          'background-color': '',
                          'border-color': ''
                      });
                
                alert('Enrollment failed: ' + (response.message || 'Please try again.'));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', {xhr: xhr, status: status, error: error});
            console.error('Response text:', xhr.responseText);
            
            let errorMessage = 'Enrollment failed. Please try again.';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                }
            } catch (e) {
                console.error('Could not parse error response');
            }
            
            button.removeClass('btn-success enrolled')
                  .addClass('btn-success')
                  .html('<i class="bi bi-plus-circle"></i> Enroll')
                  .prop('disabled', false)
                  .css({
                      'background-color': '',
                      'border-color': ''
                  });
            
            alert(errorMessage);
        }
    });
}
</script>
<style>
.sidebar { position: fixed; top: 0; left: 0; display: flex; flex-direction: column; justify-content: space-between; height: 100vh; }
.ms-220 { margin-left: 220px; }
.hover-card { transition: all 0.25s ease-in-out; cursor: pointer; }
.hover-card:hover { transform: translateY(-6px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
.nav-link:hover { background-color: rgba(255,255,255,0.1); border-radius: 8px; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

/* Enrollment button styles */
.enroll-btn {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 6px !important;
    position: relative;
    overflow: hidden;
}

.enroll-btn.enrolled {
    border-radius: 25px !important;
    padding: 8px 20px !important;
    font-weight: 500 !important;
    transform: scale(1.05);
}

.enroll-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.enroll-btn:active {
    transform: scale(0.98);
}

/* Enrolled badge styling to match button */
.enrolled-badge {
    padding: 8px 20px !important;
    font-weight: 500 !important;
    font-size: 0.875rem !important;
    background-color: #28a745 !important;
}

/* Course Materials Section */
.materials-section .card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    border: none;
}

.materials-section .card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.course-materials-item {
    transition: all 0.2s ease;
    border-radius: 8px;
    margin-bottom: 8px;
    padding: 12px;
    background: #f8f9fa;
}

.course-materials-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.course-title {
    color: #007bff !important;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e9ecef;
}
</style>