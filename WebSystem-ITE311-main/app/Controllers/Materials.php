<?php

namespace App\Controllers;

use App\Models\courseModel;
use App\Models\MaterialModel;

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

        // NOTE: Add instructor check here
        // Example: if(session()->get('role') !== 'instructor') {
        //     return redirect()->back()->with('error', 'Only instructors can upload materials.');
        // }

        $material = new MaterialModel();
        $course = new courseModel();

        if($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('material_file');

            // Validate uploaded file
            // NOTE: Adjust file size (currently 100MB) and allowed extensions as needed
            $validation = \Config\Services::validation();
            $validation->setRules([
            'material_file' => [
                'rules' => 'uploaded[material_file]|max_size[material_file,102400]|ext_in[material_file,pdf,doc,docx,ppt,pptx,zip]',
                'errors' => [
                    'uploaded' => 'Please select a file.',
                    'max_size' => 'File is too large. Maximum size is 100MB.',
                    'ext_in'  => 'Invalid file type. Allowed: pdf, doc, docx, ppt, pptx, zip.'
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

        // NOTE: Add instructor role check here
        // Example: if(session()->get('role') !== 'instructor') {
        //     return redirect()->back()->with('error', 'Unauthorized access.');
        // }

        $material = new MaterialModel();
        $file = $material->find($material_id);

        if ($file) {
            // Delete the physical file from the server
            if (file_exists(WRITEPATH . $file['file_path'])) {
                unlink(WRITEPATH . $file['file_path']);
            }

            // Delete the record from the database
            $material->delete($material_id);

            return redirect()->back()->with('success', 'Material deleted successfully.');
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
            // NOTE: ENROLLMENT CHECK - Verify user is enrolled in the course
            // Uncomment and adjust the following code to match your database structure:
            
            // $db = \Config\Database::connect();
            // $builder = $db->table('enrollments'); // Adjust table name if needed
            // $enrollment = $builder->where([
            //     'user_id' => session()->get('id'),        // Adjust column name if needed
            //     'course_id' => $file['course_id'],        // Adjust column name if needed
            //     'status' => 'active'                      // Adjust status value if needed
            // ])->get()->getRow();
            // 
            // // Allow instructors to bypass enrollment check
            // if (!$enrollment && session()->get('role') !== 'instructor') {
            //     return redirect()->back()->with('error', 'You must be enrolled in this course to download materials.');
            // }

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