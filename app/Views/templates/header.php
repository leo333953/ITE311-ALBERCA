<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .navbar {
            background-color: #0d6efd;
        }
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.15);
            border-radius: 6px;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            border-radius: 6px;
        }
        .hover-card {
            transition: all 0.25s ease-in-out;
            cursor: pointer;
        }
        .hover-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .ms-220 {
            margin-left: 220px;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">LMS Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item me-3">
                        <span class="text-white">Hello, <?= esc($role) ?></span>
                    </li>
                    <li class="nav-item me-2 position-relative">
                        <a href="#" id="notificationBtn" class="btn btn-outline-light btn-sm position-relative">
                            <i class="bi bi-bell"></i>
                            <span id="notificationBadge" class="notification-badge hidden">0</span>
                        </a>
                        <div id="notificationDropdown" class="notification-dropdown">
                            <div class="notification-header">
                                <i class="bi bi-bell-fill me-2"></i>Notifications
                            </div>
                            <div id="notificationList">
                                <div class="notification-empty">Loading...</div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 350px;
            max-height: 500px;
            overflow-y: auto;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1050;
            margin-top: 10px;
            display: none;
        }
        .notification-dropdown.show {
            display: block;
        }
        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }
        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            background-color: #e7f3ff;
            font-weight: 500;
        }
        .notification-item.unread:hover {
            background-color: #d0e7ff;
        }
        .notification-message {
            margin-bottom: 8px;
            color: #333;
        }
        .notification-time {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .notification-actions {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-mark-read {
            font-size: 0.8rem;
            padding: 6px 14px;
            border-radius: 5px;
            transition: all 0.2s;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-mark-read:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .btn-mark-read:active {
            transform: scale(0.98);
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .notification-badge.hidden {
            display: none;
        }
        .notification-empty {
            padding: 30px;
            text-align: center;
            color: #6c757d;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            const $notificationBtn = $('#notificationBtn');
            const $notificationDropdown = $('#notificationDropdown');
            const $notificationBadge = $('#notificationBadge');
            const $notificationList = $('#notificationList');
            
            // Toggle dropdown
            $notificationBtn.on('click', function(e) {
                e.stopPropagation();
                $notificationDropdown.toggleClass('show');
                if ($notificationDropdown.hasClass('show')) {
                    loadNotifications();
                }
            });
            
            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#notificationBtn, #notificationDropdown').length) {
                    $notificationDropdown.removeClass('show');
                }
            });
            
            // Load notifications
            function loadNotifications() {
                $.ajax({
                    url: '<?= base_url('notifications') ?>',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        updateNotificationBadge(response.unreadCount);
                        renderNotifications(response.notifications);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading notifications:', error);
                    }
                });
            }
            
            // Update notification badge
            function updateNotificationBadge(count) {
                if (count > 0) {
                    $notificationBadge.text(count > 99 ? '99+' : count).removeClass('hidden');
                } else {
                    $notificationBadge.addClass('hidden');
                }
            }
            
            // Render notifications
            function renderNotifications(notifications) {
                console.log('Rendering notifications:', notifications); // Debug
                
                if (!notifications || notifications.length === 0) {
                    $notificationList.html('<div class="notification-empty">No notifications</div>');
                    return;
                }
                
                let html = '';
                notifications.forEach(function(notification) {
                    // Handle is_read as boolean or 0/1
                    const isUnread = (notification.is_read === 0 || notification.is_read === false || notification.is_read === '0' || !notification.is_read);
                    const timeAgo = getTimeAgo(notification.created_at);
                    const itemClass = isUnread ? 'notification-item unread' : 'notification-item';
                    
                    html += `
                        <div class="${itemClass}" data-id="${notification.id}">
                            <div class="notification-message">${escapeHtml(notification.message)}</div>
                            <div class="notification-time">
                                <i class="bi bi-clock me-1"></i>${timeAgo}
                            </div>
                            ${isUnread ? `
                                <div class="notification-actions">
                                    <button class="btn btn-sm btn-primary btn-mark-read" onclick="markAsRead(${notification.id}, this)">
                                        <i class="bi bi-check2"></i> Mark as Read
                                    </button>
                                </div>
                            ` : `
                                <div class="notification-time text-success mt-2">
                                    <i class="bi bi-check-circle-fill me-1"></i>Read
                                </div>
                            `}
                        </div>
                    `;
                });
                
                $notificationList.html(html);
            }
            
            // Mark notification as read
            window.markAsRead = function(notificationId, button) {
                console.log('Marking notification as read:', notificationId); // Debug
                
                const $button = $(button);
                const originalHtml = $button.html();
                
                // Show loading state
                $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Marking...');
                
                $.ajax({
                    url: `<?= base_url('notifications/mark-as-read') ?>/${notificationId}`,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Show success animation
                            $button.html('<i class="bi bi-check-circle-fill"></i> Done!');
                            
                            setTimeout(function() {
                                // Remove unread styling
                                const $item = $button.closest('.notification-item');
                                $item.removeClass('unread');
                                
                                // Replace button with read status
                                $button.closest('.notification-actions').replaceWith(`
                                    <div class="notification-time text-success mt-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>Read
                                    </div>
                                `);
                                
                                // Reload to update count
                                loadNotifications();
                            }, 500);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error marking notification as read:', error);
                        $button.prop('disabled', false).html(originalHtml);
                        alert('Failed to mark notification as read. Please try again.');
                    }
                });
            };
            
            // Get time ago
            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) return 'Just now';
                if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
                if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
                if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' days ago';
                
                return date.toLocaleDateString();
            }
            
            // Escape HTML
            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }
            
            // Load notifications on page load
            loadNotifications();
            
            // Auto-refresh notifications every 30 seconds
            setInterval(function() {
                loadNotifications();
            }, 5000);
        });
    </script>
</body>
</html>
