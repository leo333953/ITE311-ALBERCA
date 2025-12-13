<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;
use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use App\Helpers\NotificationHelper;

class SoftDeletes extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $data = [
            'role' => session()->get('role'),
            'deletedCourses' => $this->getDeletedCourses(),
            'deletedUsers' => $this->getDeletedUsers(),
            'deletedMaterials' => $this->getDeletedMaterials(),
            'deletedEnrollments' => $this->getDeletedEnrollments()
        ];

        return view('templates/header', ['role' => $data['role']]) 
             . view('admin/soft_deletes', $data);
    }

    private function getDeletedCourses()
    {
        $courseModel = new CourseModel();
        return $courseModel->onlyDeleted()->findAll();
    }

    private function getDeletedUsers()
    {
        $userModel = new UserModel();
        return $userModel->onlyDeleted()->findAll();
    }

    private function getDeletedMaterials()
    {
        $materialModel = new MaterialModel();
        return $materialModel->onlyDeleted()->findAll();
    }

    private function getDeletedEnrollments()
    {
        $enrollmentModel = new EnrollmentModel();
        return $enrollmentModel->onlyDeleted()->findAll();
    }

    public function restore()
    {
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $type = $this->request->getPost('type');
        $id = $this->request->getPost('id');

        if (!$type || !$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing type or ID'
            ]);
        }

        try {
            switch ($type) {
                case 'course':
                    $model = new CourseModel();
                    break;
                case 'user':
                    $model = new UserModel();
                    break;
                case 'material':
                    $model = new MaterialModel();
                    break;
                case 'enrollment':
                    $model = new EnrollmentModel();
                    break;
                default:
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid type'
                    ]);
            }

            // Get item info before restore for notification
            $item = $model->withDeleted()->find($id);
            $itemName = $item['title'] ?? $item['name'] ?? $item['file_name'] ?? $item['full_name'] ?? 'Unknown Item';
            
            // Restore the record by setting deleted_at to null
            if ($model->update($id, ['deleted_at' => null])) {
                // Send notification about restoration
                $notificationHelper = new NotificationHelper();
                $restorerName = $notificationHelper->getUserName(session()->get('user_id'));
                $notificationHelper->notifyItemRestored(
                    ucfirst($type),
                    $itemName,
                    $restorerName
                );
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => ucfirst($type) . ' restored successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to restore ' . $type
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function permanentDelete()
    {
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $type = $this->request->getPost('type');
        $id = $this->request->getPost('id');

        if (!$type || !$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing type or ID'
            ]);
        }

        try {
            switch ($type) {
                case 'course':
                    $model = new CourseModel();
                    break;
                case 'user':
                    $model = new UserModel();
                    break;
                case 'material':
                    $model = new MaterialModel();
                    // For materials, also delete the physical file
                    $material = $model->onlyDeleted()->find($id);
                    if ($material && file_exists(WRITEPATH . $material['file_path'])) {
                        unlink(WRITEPATH . $material['file_path']);
                    }
                    break;
                case 'enrollment':
                    $model = new EnrollmentModel();
                    break;
                default:
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid type'
                    ]);
            }

            // Get item info before permanent deletion for notification
            $item = $model->withDeleted()->find($id);
            $itemName = $item['title'] ?? $item['name'] ?? $item['file_name'] ?? $item['full_name'] ?? 'Unknown Item';
            
            // Permanently delete the record
            if ($model->delete($id, true)) {
                // Send notification about permanent deletion
                $notificationHelper = new NotificationHelper();
                $deleterName = $notificationHelper->getUserName(session()->get('user_id'));
                $notificationHelper->notifyItemPermanentlyDeleted(
                    ucfirst($type),
                    $itemName,
                    $deleterName
                );
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => ucfirst($type) . ' permanently deleted'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to permanently delete ' . $type
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function bulkRestore()
    {
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $type = $this->request->getPost('type');

        if (!$type) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing type'
            ]);
        }

        try {
            switch ($type) {
                case 'courses':
                    $model = new CourseModel();
                    break;
                case 'users':
                    $model = new UserModel();
                    break;
                case 'materials':
                    $model = new MaterialModel();
                    break;
                case 'enrollments':
                    $model = new EnrollmentModel();
                    break;
                default:
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid type'
                    ]);
            }

            // Restore all deleted records of this type
            $deletedRecords = $model->onlyDeleted()->findAll();
            $restoredCount = 0;

            foreach ($deletedRecords as $record) {
                if ($model->update($record['id'], ['deleted_at' => null])) {
                    $restoredCount++;
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Restored $restoredCount " . rtrim($type, 's') . "(s)"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}