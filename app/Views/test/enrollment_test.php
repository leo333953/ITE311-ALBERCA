<!DOCTYPE html>
<html>
<head>
    <title>Enrollment System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Enrollment System Test</h1>
        
        <div class="alert alert-info">
            <h4>Test Instructions:</h4>
            <ol>
                <li>Make sure you're logged in as a student</li>
                <li>Fill out the form below to test enrollment</li>
                <li>Check the response for success/error messages</li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Test Enrollment Form</h3>
            </div>
            <div class="card-body">
                <form id="testEnrollmentForm">
                    <input type="hidden" name="course_id" value="1">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control" name="student_id" value="2024-TEST-001" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" value="Test Student" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Course Program</label>
                                <select class="form-control" name="course_program" required>
                                    <option value="Bachelor of Science in Information Technology">BS Information Technology</option>
                                    <option value="Bachelor of Science in Computer Science">BS Computer Science</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Year Level</label>
                                <select class="form-control" name="year_level" required>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="test@student.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone_number" value="1234567890" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" required>Test Address, Test City</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="2000-01-01" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Test Submit Enrollment</button>
                </form>
            </div>
        </div>

        <div id="result" class="mt-4"></div>
    </div>

    <script>
    $('#testEnrollmentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= base_url('enrollments/submit') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#result').html('<div class="alert alert-success"><h4>Success!</h4><p>' + response.message + '</p></div>');
                } else {
                    $('#result').html('<div class="alert alert-danger"><h4>Error!</h4><p>' + response.message + '</p>' + 
                        (response.errors ? '<pre>' + JSON.stringify(response.errors, null, 2) + '</pre>' : '') + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#result').html('<div class="alert alert-danger"><h4>AJAX Error!</h4><p>' + error + '</p><pre>' + xhr.responseText + '</pre></div>');
            }
        });
    });
    </script>
</body>
</html>