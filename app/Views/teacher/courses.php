<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Course Catalog - Teacher View</h3>
                    <button class="btn btn-primary" onclick="showCreateModal()">
                        <i class="bi bi-plus-circle"></i> Add New Course
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search courses by title, description, or instructor...">
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
                                <select id="instructorFilter" class="form-select">
                                    <option value="">All Instructors</option>
                                    <option value="assigned">With Instructor</option>
                                    <option value="unassigned">No Instructor</option>
                                </select>
                                <select id="sortBy" class="form-select">
                                    <option value="title">Sort by Title</option>
                                    <option value="created_at">Sort by Date</option>
                                    <option value="instructor_id">Sort by Instructor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results Info -->
                    <div id="searchInfo" class="alert alert-info d-none">
                        <i class="bi bi-info-circle"></i> <span id="searchInfoText"></span>
                    </div>

                    <!-- Courses Table -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="coursesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Course Title</th>
                                    <th>Description</th>
                                    <th>Instructor</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($courses)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No courses found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?= $course['id'] ?></td>
                                            <td><strong><?= esc($course['title']) ?></strong></td>
                                            <td><?= esc(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?></td>
                                            <td><?= $course['instructor_id'] ? 'Instructor ID: ' . $course['instructor_id'] : 'Not Assigned' ?></td>
                                            <td><?= isset($course['created_at']) ? date('M d, Y', strtotime($course['created_at'])) : 'N/A' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-info" onclick="viewCourse(<?= $course['id'] ?>)">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="editCourse(<?= $course['id'] ?>)">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteCourse(<?= $course['id'] ?>)">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                    <a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-upload"></i> Upload Materials
                                                    </a>
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

<!-- Create/Edit Course Modal -->
<div class="modal fade" id="courseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalTitle">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="courseForm">
                    <input type="hidden" id="courseId" name="course_id">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                    
                    <div class="mb-3">
                        <label for="courseTitle" class="form-label">Course Title *</label>
                        <input type="text" class="form-control" id="courseTitle" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="courseDescription" class="form-label">Description *</label>
                        <textarea class="form-control" id="courseDescription" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="instructorId" class="form-label">Instructor ID (Optional)</label>
                        <input type="number" class="form-control" id="instructorId" name="instructor_id" placeholder="Leave empty if not assigned">
                        <small class="text-muted">Enter the user ID of the instructor, or leave empty</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCourse()">Save Course</button>
            </div>
        </div>
    </div>
</div>

<!-- View Course Modal -->
<div class="modal fade" id="viewCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Course Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="courseDetails">
                <!-- Course details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let isEditMode = false;
let originalTableData = [];
let isServerSearchActive = false;

// Initialize on document ready
$(document).ready(function() {
    // Store original table data for client-side filtering
    storeOriginalTableData();
    
    // Set up event listeners
    setupSearchAndFilters();
});

function storeOriginalTableData() {
    originalTableData = [];
    $('#coursesTable tbody tr').each(function() {
        if (!$(this).find('td').first().text().includes('No courses found')) {
            const row = {
                element: $(this).clone(),
                id: $(this).find('td:eq(0)').text().trim(),
                title: $(this).find('td:eq(1)').text().trim().toLowerCase(),
                description: $(this).find('td:eq(2)').text().trim().toLowerCase(),
                instructor: $(this).find('td:eq(3)').text().trim().toLowerCase(),
                created: $(this).find('td:eq(4)').text().trim(),
                hasInstructor: !$(this).find('td:eq(3)').text().includes('Not Assigned')
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

    // Instructor filter
    $('#instructorFilter').on('change', function() {
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
    const instructorFilter = $('#instructorFilter').val();
    const sortBy = $('#sortBy').val();
    
    let filteredData = [...originalTableData];
    
    // Apply search filter
    if (searchTerm) {
        filteredData = filteredData.filter(row => 
            row.title.includes(searchTerm) || 
            row.description.includes(searchTerm) || 
            row.instructor.includes(searchTerm)
        );
    }
    
    // Apply instructor filter
    if (instructorFilter === 'assigned') {
        filteredData = filteredData.filter(row => row.hasInstructor);
    } else if (instructorFilter === 'unassigned') {
        filteredData = filteredData.filter(row => !row.hasInstructor);
    }
    
    // Apply sorting
    filteredData.sort((a, b) => {
        switch (sortBy) {
            case 'title':
                return a.title.localeCompare(b.title);
            case 'created_at':
                return new Date(b.created) - new Date(a.created);
            case 'instructor_id':
                return a.instructor.localeCompare(b.instructor);
            default:
                return 0;
        }
    });
    
    // Update table
    updateTable(filteredData, searchTerm, false);
}

function performServerSearch() {
    const searchTerm = $('#searchInput').val().trim();
    const instructorFilter = $('#instructorFilter').val();
    const sortBy = $('#sortBy').val();
    
    if (!searchTerm && !instructorFilter) {
        alert('Please enter a search term or select a filter');
        return;
    }
    
    isServerSearchActive = true;
    showLoading();
    
    $.ajax({
        url: '<?= base_url('courses/search-admin') ?>',
        method: 'GET',
        data: {
            search: searchTerm,
            instructor_filter: instructorFilter,
            sort_by: sortBy
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                renderServerResults(response.courses, searchTerm);
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

function renderServerResults(courses, searchTerm) {
    const tbody = $('#coursesTable tbody');
    tbody.empty();
    
    if (courses.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="bi bi-search"></i> No courses found matching your search criteria
                </td>
            </tr>
        `);
    } else {
        courses.forEach(course => {
            const row = `
                <tr>
                    <td>${course.id}</td>
                    <td><strong>${escapeHtml(course.title)}</strong></td>
                    <td>${escapeHtml(course.description.substring(0, 100))}${course.description.length > 100 ? '...' : ''}</td>
                    <td>${course.instructor_id ? 'Instructor ID: ' + course.instructor_id : 'Not Assigned'}</td>
                    <td>${course.created_at ? new Date(course.created_at).toLocaleDateString() : 'N/A'}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info" onclick="viewCourse(${course.id})">
                                <i class="bi bi-eye"></i> View
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="editCourse(${course.id})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                            <a href="<?= base_url('admin/course/') ?>${course.id}/upload" class="btn btn-sm btn-success">
                                <i class="bi bi-upload"></i> Upload Materials
                            </a>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Show search info
    showSearchInfo(`Found ${courses.length} course(s) from server search${searchTerm ? ' for "' + escapeHtml(searchTerm) + '"' : ''}`);
}

function updateTable(filteredData, searchTerm, isServerResult) {
    const tbody = $('#coursesTable tbody');
    tbody.empty();
    
    if (filteredData.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="bi bi-search"></i> No courses found${searchTerm ? ' matching "' + escapeHtml(searchTerm) + '"' : ''}
                </td>
            </tr>
        `);
        showSearchInfo(`No courses found${searchTerm ? ' matching "' + escapeHtml(searchTerm) + '"' : ''}`);
    } else {
        filteredData.forEach(row => {
            tbody.append(row.element);
        });
        
        const filterInfo = [];
        if (searchTerm) filterInfo.push(`search: "${escapeHtml(searchTerm)}"`);
        if ($('#instructorFilter').val()) filterInfo.push(`instructor filter: ${$('#instructorFilter option:selected').text()}`);
        
        showSearchInfo(`Showing ${filteredData.length} of ${originalTableData.length} courses${filterInfo.length ? ' (' + filterInfo.join(', ') + ')' : ''}`);
    }
}

function clearAllFilters() {
    $('#searchInput').val('');
    $('#instructorFilter').val('');
    $('#sortBy').val('title');
    isServerSearchActive = false;
    
    // Restore original table
    const tbody = $('#coursesTable tbody');
    tbody.empty();
    originalTableData.forEach(row => {
        tbody.append(row.element);
    });
    
    hideSearchInfo();
}

function showLoading() {
    const tbody = $('#coursesTable tbody');
    tbody.html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Searching database...</p>
            </td>
        </tr>
    `);
}

function showError(message) {
    const tbody = $('#coursesTable tbody');
    tbody.html(`
        <tr>
            <td colspan="6" class="text-center text-danger py-4">
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

function showCreateModal() {
    isEditMode = false;
    $('#courseModalTitle').text('Add New Course');
    $('#courseForm')[0].reset();
    $('#courseId').val('');
    $('#courseModal').modal('show');
}

function editCourse(id) {
    isEditMode = true;
    $('#courseModalTitle').text('Edit Course');
    
    // Get course details
    $.ajax({
        url: '<?= base_url('courses/get/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const course = response.course;
                $('#courseId').val(course.id);
                $('#courseTitle').val(course.title);
                $('#courseDescription').val(course.description);
                $('#instructorId').val(course.instructor_id || '');
                $('#courseModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while loading course details.');
        }
    });
}

function saveCourse() {
    const formData = new FormData($('#courseForm')[0]);
    const courseId = $('#courseId').val();
    
    // Add CSRF token
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    let url = '<?= base_url('courses/create') ?>';
    if (isEditMode && courseId) {
        url = '<?= base_url('courses/update/') ?>' + courseId;
    }
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert(response.message);
                $('#courseModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + response.message);
                if (response.errors) {
                    console.log('Validation errors:', response.errors);
                }
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error:', xhr.responseText);
            alert('An error occurred while saving the course: ' + error);
        }
    });
}

function viewCourse(id) {
    $.ajax({
        url: '<?= base_url('courses/get/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const course = response.course;
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Course Information</h6>
                            <p><strong>ID:</strong> ${course.id}</p>
                            <p><strong>Title:</strong> ${course.title}</p>
                            <p><strong>Instructor ID:</strong> ${course.instructor_id || 'Not Assigned'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Dates</h6>
                            <p><strong>Created:</strong> ${course.created_at ? new Date(course.created_at).toLocaleDateString() : 'N/A'}</p>
                            <p><strong>Updated:</strong> ${course.updated_at ? new Date(course.updated_at).toLocaleDateString() : 'N/A'}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Description</h6>
                    <p>${course.description}</p>
                `;
                
                $('#courseDetails').html(html);
                $('#viewCourseModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while loading course details.');
        }
    });
}

function deleteCourse(id) {
    if (confirm('Are you sure you want to delete this course? This record will be soft deleted and can be recovered from the Soft Deletes section.')) {
        $.ajax({
            url: '<?= base_url('courses/delete/') ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Remove the course card from the display immediately
                    $('button[onclick="deleteCourse(' + id + ')"]').closest('.col-md-4').fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if no courses are left and show "no courses" message
                        if ($('#coursesContainer .col-md-4:visible').length === 0) {
                            $('#coursesContainer').html('<div class="col-12"><div class="alert alert-info text-center">No courses found</div></div>');
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
                alert('An error occurred while deleting the course.');
            }
        });
    }
}
</script>