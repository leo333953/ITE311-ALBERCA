<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\MaterialModel;
use App\Helpers\NotificationHelper;

/**
 * Materials Controller
 * 
 * This controller handles:
 * - Material upload for courses
 * - Material deletion (restricted to instructors)
 * - Material download (for enrolled students and instructors)
 * 
 * NOTES:
 * - Enrollment checks query the 'enrollments' table created in Lab6
 * - Adjust table/column names below to match your database schema
 * - Default columns expected: enrollments.user_id, enrollments.course_id, enrollments.status
 * - Materials are stored in writable/uploads/materials/
 */
class Materials extends BaseController
{
    /**
     * Upload material to a course
     * 
     * NOTE: This method should verify instructor permissions
     * Adjust validation rules and file types as needed
     * 
     * @param int $course_id The ID of the course
     * @return mixed Redirect or view response
     */
    public function upload($course_id) 
    {
        // Check if user is logged in
        if(session()->get('isLoggedIn') !== true) {
            return redirect()->to('/login');
        }

        // Check if user is teacher or admin
        $userRole = session()->get('role');
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return redirect()->back()->with('error', 'Only teachers and admins can upload materials.');
        }

        $material = new MaterialModel();
        $course = new CourseModel();

        if($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('material_file');

            // Validate uploaded file - Only PDF and PPT files allowed
            $validation = \Config\Services::validation();
            $validation->setRules([
            'material_file' => [
                'rules' => 'uploaded[material_file]|max_size[material_file,102400]|ext_in[material_file,pdf,ppt]',
                'errors' => [
                    'uploaded' => 'Please select a file.',
                    'max_size' => 'File is too large. Maximum size is 100MB.',
                    'ext_in'  => 'Invalid file type. Only PDF and PowerPoint files (PDF, PPT) are allowed.'
                ]
            ]
            ]);
            
            if(!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Process the file upload
            if ($file->isValid()) {
                // Generate random name for security
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads/materials/', $newName);

                // Prepare data for database insertion
                // NOTE: Adjust column names to match your materials table
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/materials/' . $newName,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $material->insertMaterial($data);
                
                // Send notification to enrolled students about new material
                $notificationHelper = new NotificationHelper();
                $courseData = $course->find($course_id);
                $uploaderName = session()->get('name') ?? 'Unknown User';
                $notifiedCount = $notificationHelper->notifyEnrolledStudents(
                    $course_id,
                    $file->getClientName(),
                    $courseData['title'] ?? 'Unknown Course',
                    $uploaderName
                );
                
                log_message('info', "Material uploaded: {$file->getClientName()} - {$notifiedCount} students notified");
                
                return redirect()->to(current_url())->with('success', 'Material uploaded successfully.');
            }

            return redirect()->back()->withInput()->with('error', 'Failed to upload material. Please try again.');
        }   
        
        // Display upload form
        $role = session()->get('role');
        $courseData = $course->find($course_id);
        $materials = $material->getMaterialsByCourse($course_id);
        return view('templates/header', ['role' => $role]) . view('upload', [
            'course' => $courseData,
            'materials' => $materials,
            'user_role' => $role
        ]);
    }

    /**
     * Delete a material file
     * 
     * NOTE: Should be restricted to instructors only
     * Adjust the role check to match your database role field
     * 
     * @param int $material_id The ID of the material to delete
     * @return mixed Redirect response
     */
    public function delete($material_id) 
    {
        // Check if user is logged in
        if(session()->get('isLoggedIn') !== true) {
            return redirect()->to('/login');
        }

        // Check if user is teacher or admin
        $userRole = session()->get('role');
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return redirect()->back()->with('error', 'Only teachers and admins can delete materials.');
        }

        $material = new MaterialModel();
        $file = $material->find($material_id);

        if ($file) {
            // Get course and user info for notification
            $course = new CourseModel();
            $courseData = $course->find($file['course_id']);
            $deleterName = (new \App\Models\UserModel())->find(session()->get('user_id'))['name'] ?? 'Unknown User';
            
            // Soft delete - only mark as deleted in database, keep physical file
            // This allows for potential recovery of materials
            $material->delete($material_id);

            // Send notification about material deletion
            $notificationHelper = new NotificationHelper();
            $notificationHelper->notifyMaterialDeleted(
                $file['file_name'],
                $courseData['title'] ?? 'Unknown Course',
                $deleterName
            );

            return redirect()->back()->with('success', 'Material deleted successfully (can be recovered by admin).');
        } else {
            return redirect()->back()->with('error', 'File not found.');
        }
    }
 
    /**
     * Download a material file
     * 
     * NOTE: Enrollment check verifies if the user is enrolled in the course
     * Adjust the query to match your enrollments table structure from Lab6
     * Expected columns: user_id, course_id, status (adjust as needed)
     * 
     * @param int $material_id The ID of the material to download
     * @return mixed Download response or redirect
     */
    public function download($material_id)
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to('/login');
        }

        $material = new MaterialModel();
        $file = $material->find($material_id);

        if ($file) {
            // ENROLLMENT CHECK - Verify user is enrolled in the course
            $db = \Config\Database::connect();
            $builder = $db->table('enrollments');
            $enrollment = $builder->where([
                'user_id' => session()->get('user_id'),
                'course_id' => $file['course_id'],
            ])->whereIn('status', ['approved', 'enrolled'])
             ->where('deleted_at IS NULL')
             ->get()->getRow();
            
            // Allow teachers and admins to bypass enrollment check
            $userRole = session()->get('role');
            if (!$enrollment && !in_array($userRole, ['teacher', 'admin'])) {
                return redirect()->back()->with('error', 'You must be enrolled in this course to download materials.');
            }

            // Construct the correct file path inside writable folder
            $filePath = WRITEPATH . $file['file_path'];

            if (file_exists($filePath)) {
                return $this->response->download($filePath, $file['file_name']);
            } else {
                return redirect()->back()->with('error', 'File not found on server.');
            }
        }

        return redirect()->back()->with('error', 'File record not found.');
    }
}