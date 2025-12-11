<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'message',
        'is_read',
        'created_at',
    ];

    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->countAllResults();
    }

    public function getNotificationsForUser($userId, $limit = 5, $offset = 0)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }

    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => true]);
    }

    public function createNotification($userId, $message)
    {
        $data = [
            'user_id' => $userId,
            'message' => $message,
            'is_read' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->insert($data);
    }
}