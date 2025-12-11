<div class="container-fluid ms-220 p-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">
                <i class="bi bi-journal-check"></i> My Enrolled Courses
            </h2>
            <p class="text-muted">
                <i class="bi bi-info-circle"></i> These are the courses you are currently enrolled in.
            </p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form id="searchForm" class="d-flex">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Search your enrolled courses..." name="search_term">
                    <button class="btn btn-primary" type="submit" id="serverSearchBtn">
                        <i class="bi bi-search"></i> Server Search
                    </button>
                </div>
            </form>
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> Type to filter instantly, or click "Server Search" for database search
            </small>
        </div>
        <div class="col-md-6 text-end">
            <button id="clearSearch" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear Search
            </button>
        </div>
    </div>

    <!-- Courses Grid -->
    <div id="coursesContainer" class="row">
        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4">
                    <div class="card course-card hover-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title text-primary mb-0">
                                    <i class="bi bi-journal-text"></i> 
                                    <?= esc($course['title']) ?>
                                </h5>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Enrolled
                                </span>
                            </div>
                            <p class="card-text flex-grow-1">
                                <?= esc(substr($course['description'], 0, 150)) ?>
                                <?= strlen($course['description']) > 150 ? '...' : '' ?>
                            </p>
                            <button class="btn btn-primary mt-auto view-details-btn" 
                                    data-course-id="<?= $course['id'] ?>"
                                    data-course-title="<?= esc($course['title']) ?>"
                                    data-course-description="<?= esc($course['description']) ?>">
                                <i class="bi bi-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> You are not enrolled in any courses yet.
                    <br>
                    <small class="text-muted">Browse available courses and enroll to get started!</small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Course Details Modal -->
<div class="modal fade" id="courseDetailsModal" tabindex="-1" aria-labelledby="courseDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="courseDetailsModalLabel">
                    <i class="bi bi-journal-text"></i> Course Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 id="modalCourseTitle" class="text-primary mb-0"></h4>
                    <span class="badge bg-success fs-6">
                        <i class="bi bi-check-circle-fill"></i> Enrolled
                    </span>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Description:</h6>
                    <p id="modalCourseDescription" class="text-justify"></p>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> You are currently enrolled in this course.
                </div>
                <input type="hidden" id="modalCourseId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .course-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .course-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
    }
    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
    }
    .view-details-btn {
        width: 100%;
    }
</style>

<script>
$(document).ready(function() {
    // View Details Button
    $(document).on('click', '.view-details-btn', function() {
        const courseId = $(this).data('course-id');
        const courseTitle = $(this).data('course-title');
        const courseDescription = $(this).data('course-description');
        
        $('#modalCourseId').val(courseId);
        $('#modalCourseTitle').text(courseTitle);
        $('#modalCourseDescription').text(courseDescription);
        
        $('#courseDetailsModal').modal('show');
    });
    
    // Track if server search has been performed
    let serverSearchActive = false;
    
    // Client-side real-time search (filters as you type)
    $('#searchInput').on('keyup', function(e) {
        // Don't run client-side search if Enter key is pressed (will trigger server search)
        if (e.keyCode === 13) {
            return;
        }
        
        // Only run if server search is not active
        if (serverSearchActive) {
            return;
        }
        
        const searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all courses when search is empty
            $('.course-card').closest('.col-md-4').show();
            $('#coursesContainer').find('.no-results-message').remove();
            return;
        }
        
        // Filter courses based on search term
        $('.course-card').each(function() {
            const courseTitle = $(this).find('.card-title').text().toLowerCase();
            const courseDescription = $(this).find('.card-text').text().toLowerCase();
            
            if (courseTitle.includes(searchTerm) || courseDescription.includes(searchTerm)) {
                $(this).closest('.col-md-4').fadeIn(200);
            } else {
                $(this).closest('.col-md-4').fadeOut(200);
            }
        });
        
        // Check if any courses are visible
        setTimeout(function() {
            const visibleCourses = $('.course-card:visible').length;
            const $container = $('#coursesContainer');
            
            // Remove any existing "no results" message
            $container.find('.no-results-message').remove();
            
            if (visibleCourses === 0) {
                $container.append(`
                    <div class="col-12 no-results-message">
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-search"></i> No enrolled courses found matching "${escapeHtml(searchTerm)}".
                            <br>
                            <small class="text-muted">Try a different search term or use Server Search.</small>
                        </div>
                    </div>
                `);
            }
        }, 250);
    });
    
    // Search Form Submit - Server-side search
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const searchTerm = $('#searchInput').val().trim();
        
        if (!searchTerm) {
            alert('Please enter a search term');
            return;
        }
        
        // Mark that server search is active
        serverSearchActive = true;
        
        // Show loading indicator
        const $container = $('#coursesContainer');
        $container.html(`
            <div class="col-12 text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Searching database for "${escapeHtml(searchTerm)}"...</p>
            </div>
        `);
        
        $.ajax({
            url: '<?= base_url('courses/search') ?>',
            method: 'GET',
            data: { search_term: searchTerm },
            dataType: 'json',
            success: function(data) {
                console.log('Server search results:', data);
                renderCourses(data, searchTerm);
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                console.error('Response:', xhr.responseText);
                serverSearchActive = false;
                $container.html(`
                    <div class="col-12">
                        <div class="alert alert-danger text-center">
                            <i class="bi bi-exclamation-triangle"></i> An error occurred while searching.
                            <br>
                            <small class="text-muted">${error}</small>
                            <br>
                            <button class="btn btn-primary mt-2" onclick="location.reload()">Reload Page</button>
                        </div>
                    </div>
                `);
            }
        });
    });
    
    // Clear Search Button
    $('#clearSearch').on('click', function() {
        $('#searchInput').val('');
        serverSearchActive = false;
        
        if ($('.course-card').length === 0) {
            // If server search was performed, reload page
            location.reload();
        } else {
            // If client-side search, just show all courses
            $('.course-card').closest('.col-md-4').fadeIn(200);
            $('#coursesContainer').find('.no-results-message').remove();
        }
    });
    
    // Render Courses Function for Server-Side Search Results
    function renderCourses(courses, searchTerm = '') {
        const $container = $('#coursesContainer');
        $container.empty();
        
        if (courses.length === 0) {
            $container.html(`
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-search"></i> No enrolled courses found${searchTerm ? ' matching "' + escapeHtml(searchTerm) + '"' : ''}.
                        <br>
                        <small class="text-muted">Try a different search term or <a href="#" onclick="location.reload(); return false;">reload the page</a> to see all enrolled courses.</small>
                    </div>
                </div>
            `);
            return;
        }
        
        // Add result count
        $container.append(`
            <div class="col-12 mb-3">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Found <strong>${courses.length}</strong> course(s)${searchTerm ? ' matching "' + escapeHtml(searchTerm) + '"' : ''}.
                </div>
            </div>
        `);
        
        courses.forEach(function(course) {
            const description = course.description || '';
            const truncated = description.length > 150 ? description.substring(0, 150) + '...' : description;
            
            const courseHtml = `
                <div class="col-md-4 mb-4">
                    <div class="card course-card hover-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title text-primary mb-0">
                                    <i class="bi bi-journal-text"></i> 
                                    ${escapeHtml(course.title)}
                                </h5>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Enrolled
                                </span>
                            </div>
                            <p class="card-text flex-grow-1">
                                ${escapeHtml(truncated)}
                            </p>
                            <button class="btn btn-primary mt-auto view-details-btn" 
                                    data-course-id="${course.id}"
                                    data-course-title="${escapeHtml(course.title)}"
                                    data-course-description="${escapeHtml(course.description)}">
                                <i class="bi bi-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $container.append(courseHtml);
        });
    }
    
    // Escape HTML function
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
});
</script>
