<?php

namespace App\Controllers;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    public function get()
    {
        $notificationModel = new NotificationModel();
        $userId = session()->get('user_id');

        $notifications = $notificationModel->getNotificationsForUser($userId);
        $unreadCount = $notificationModel->getUnreadCount($userId);

        return $this->response->setJSON([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    public function markAsRead($notificationId)
    {
        $notificationModel = new NotificationModel();
        $notificationModel->markAsRead($notificationId);

        return $this->response->setJSON(['status' => 'success']);
    }
}