<div class="container mt-5">
    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
    <h2 class="mb-4">Upload Material for: <?= esc($course['course_name'] ?? 'Course') ?></h2>

    <!-- Display success or error messages -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Upload Form -->
    <form action="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group mb-3">
            <label for="material_file" class="form-label">Select Material File (PDF, DOC, DOCX, PPT, PPTX, ZIP)</label>
            <input type="file" name="material_file" id="material_file" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload Material</button>
    </form>

    <!-- Display existing materials with download and delete buttons for teachers and admins -->
    <?php if(($user_role === 'teacher' || $user_role === 'admin') && !empty($materials)): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-files me-2"></i>Uploaded Materials
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Uploaded Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($materials as $material): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-file-earmark me-2"></i>
                                        <?= esc($material['file_name']) ?>
                                    </td>
                                    <td>
                                        <?= isset($material['created_at']) ? date('M d, Y H:i', strtotime($material['created_at'])) : 'N/A' ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('materials/download/'.$material['id']) ?>" 
                                           class="btn btn-primary btn-sm me-2">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                        <a href="<?= base_url('materials/delete/'.$material['id']) ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this material?');">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php elseif(($user_role === 'teacher' || $user_role === 'admin') && empty($materials)): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-body text-center py-4">
                <i class="bi bi-folder-x text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-2">No materials uploaded yet for this course.</p>
            </div>
        </div>
    <?php endif; ?>
</div>