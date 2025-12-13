<?php

namespace App\Helpers;

use App\Models\NotificationModel;
use App\Models\UserModel;

class NotificationHelper
{
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser($userId, $message, $type = 'info')
    {
        return $this->notificationModel->createNotification($userId, $message);
    }

    /**
     * Send notification to all users with a specific role
     */
    public function sendToRole($role, $message, $excludeUserId = null)
    {
        $users = $this->userModel->where('role', $role);
        
        if ($excludeUserId) {
            $users = $users->where('id !=', $excludeUserId);
        }
        
        $users = $users->findAll();
        
        foreach ($users as $user) {
            $this->sendToUser($user['id'], $message);
        }
        
        return count($users);
    }

    /**
     * Send notification to all admins
     */
    public function sendToAdmins($message, $excludeUserId = null)
    {
        return $this->sendToRole('admin', $message, $excludeUserId);
    }

    /**
     * Send notification to all teachers
     */
    public function sendToTeachers($message, $excludeUserId = null)
    {
        return $this->sendToRole('teacher', $message, $excludeUserId);
    }

    /**
     * Send notification to all students
     */
    public function sendToStudents($message, $excludeUserId = null)
    {
        return $this->sendToRole('student', $message, $excludeUserId);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultiple($userIds, $message)
    {
        $count = 0;
        foreach ($userIds as $userId) {
            if ($this->sendToUser($userId, $message)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get user name by ID for notification messages
     */
    public function getUserName($userId)
    {
        $user = $this->userModel->find($userId);
        return $user ? $user['name'] : 'Unknown User';
    }

    /**
     * Get user email by ID
     */
    public function getUserEmail($userId)
    {
        $user = $this->userModel->find($userId);
        return $user ? $user['email'] : 'unknown@email.com';
    }

    // Predefined notification templates for common actions

    /**
     * Course-related notifications
     */
    public function notifyCourseCreated($courseTitle, $creatorId)
    {
        $creatorName = $this->getUserName($creatorId);
        $message = "New course '{$courseTitle}' has been created by {$creatorName}";
        
        // Notify all users except creator
        $this->sendToAdmins($message, $creatorId);
        $this->sendToTeachers($message, $creatorId);
        $this->sendToStudents($message, $creatorId);
    }

    public function notifyCourseUpdated($courseTitle, $updaterId)
    {
        $updaterName = $this->getUserName($updaterId);
        $message = "Course '{$courseTitle}' has been updated by {$updaterName}";
        
        $this->sendToAdmins($message, $updaterId);
        $this->sendToTeachers($message, $updaterId);
        $this->sendToStudents($message, $updaterId);
    }

    public function notifyCourseDeleted($courseTitle, $deleterId)
    {
        $deleterName = $this->getUserName($deleterId);
        $message = "Course '{$courseTitle}' has been deleted by {$deleterName}";
        
        $this->sendToAdmins($message, $deleterId);
        $this->sendToTeachers($message, $deleterId);
    }

    /**
     * Enrollment-related notifications
     */
    public function notifyEnrollmentSubmitted($studentName, $courseProgram, $studentId)
    {
        $message = "New enrollment application submitted by {$studentName} for {$courseProgram} (Student ID: {$studentId})";
        
        $this->sendToAdmins($message);
        $this->sendToTeachers($message);
    }

    public function notifyEnrollmentApproved($studentId, $approverName, $courseProgram)
    {
        // Find student user ID by enrollment
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->where('student_number', $studentId)->first();
        
        if ($enrollment) {
            // Try to find user by email
            $user = $this->userModel->where('email', $enrollment['email'])->first();
            if ($user) {
                $message = "Your enrollment application for {$courseProgram} has been approved by {$approverName}";
                $this->sendToUser($user['id'], $message);
            }
        }
        
        // Also notify admins
        $message = "Enrollment for Student ID {$studentId} ({$courseProgram}) has been approved by {$approverName}";
        $this->sendToAdmins($message);
    }

    public function notifyEnrollmentRejected($studentId, $rejecterName, $courseProgram, $reason)
    {
        // Find student user ID by enrollment
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->where('student_number', $studentId)->first();
        
        if ($enrollment) {
            // Try to find user by email
            $user = $this->userModel->where('email', $enrollment['email'])->first();
            if ($user) {
                $message = "Your enrollment application for {$courseProgram} has been rejected by {$rejecterName}. Reason: {$reason}";
                $this->sendToUser($user['id'], $message);
            }
        }
        
        // Also notify admins
        $message = "Enrollment for Student ID {$studentId} ({$courseProgram}) has been rejected by {$rejecterName}";
        $this->sendToAdmins($message);
    }

    public function notifyEnrollmentDeleted($studentName, $deleterName, $courseProgram)
    {
        $message = "Enrollment record for {$studentName} ({$courseProgram}) has been deleted by {$deleterName}";
        $this->sendToAdmins($message);
    }

    /**
     * User management notifications
     */
    public function notifyUserCreated($userName, $userRole, $creatorName)
    {
        $message = "New {$userRole} account created for {$userName} by {$creatorName}";
        $this->sendToAdmins($message);
    }

    public function notifyUserUpdated($userName, $updaterName)
    {
        $message = "User account for {$userName} has been updated by {$updaterName}";
        $this->sendToAdmins($message);
    }

    public function notifyUserDeleted($userName, $deleterName)
    {
        $message = "User account for {$userName} has been deleted by {$deleterName}";
        $this->sendToAdmins($message);
    }

    /**
     * Material/File upload notifications
     */
    public function notifyMaterialUploaded($fileName, $courseTitle, $uploaderName)
    {
        $message = "New material '{$fileName}' uploaded to course '{$courseTitle}' by {$uploaderName}";
        
        $this->sendToAdmins($message);
        $this->sendToTeachers($message);
        $this->sendToStudents($message);
    }

    public function notifyMaterialDeleted($fileName, $courseTitle, $deleterName)
    {
        $message = "Material '{$fileName}' has been deleted from course '{$courseTitle}' by {$deleterName}";
        
        $this->sendToAdmins($message);
        $this->sendToTeachers($message);
    }

    /**
     * System notifications
     */
    public function notifyLogin($userName, $role)
    {
        $message = "{$role} {$userName} has logged into the system";
        $this->sendToAdmins($message);
    }

    public function notifyLogout($userName, $role)
    {
        $message = "{$role} {$userName} has logged out of the system";
        $this->sendToAdmins($message);
    }

    /**
     * Soft delete notifications
     */
    public function notifyItemRestored($itemType, $itemName, $restorerName)
    {
        $message = "{$itemType} '{$itemName}' has been restored from trash by {$restorerName}";
        $this->sendToAdmins($message);
    }

    public function notifyItemPermanentlyDeleted($itemType, $itemName, $deleterName)
    {
        $message = "{$itemType} '{$itemName}' has been permanently deleted by {$deleterName}";
        $this->sendToAdmins($message);
    }
}