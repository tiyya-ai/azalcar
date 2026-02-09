document.addEventListener('DOMContentLoaded', function () {
    // Check if user is authenticated (variable set in layout)
    if (typeof window.userAuthenticated === 'undefined' || !window.userAuthenticated) return;

    let lastNotificationId = null;

    function checkNotifications(showAlert = true) {
        const url = '/notifications-live-data' + (lastNotificationId ? '?last_id=' + lastNotificationId : '');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                // Update badges
                const notificationBadge = document.querySelector('.notification-badge');
                if (notificationBadge) {
                    if (data.count > 0) {
                        notificationBadge.textContent = data.count > 99 ? '99+' : data.count;
                        notificationBadge.style.display = 'flex';
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                }

                const messageBadge = document.querySelector('.message-badge');
                if (messageBadge) {
                    if (data.unread_messages_count > 0) {
                        messageBadge.textContent = data.unread_messages_count > 99 ? '99+' : data.unread_messages_count;
                        messageBadge.style.display = 'flex';
                    } else {
                        messageBadge.style.display = 'none';
                    }
                }

                if (data.latest && data.latest.id) {
                    // If we didn't have an ID yet, just set it (page load)
                    if (!lastNotificationId) {
                        lastNotificationId = data.latest.id;
                        return;
                    }

                    // If ID is new, show alert
                    if (data.latest.id !== lastNotificationId) {
                        lastNotificationId = data.latest.id;
                        if (showAlert) {
                            showNotificationPopup(data.latest);
                        }
                    }
                }
            })
            .catch(err => console.error('Notification poll error', err));
    }

    function showNotificationPopup(notification) {
        // Try Swal first
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: notification.title,
                text: notification.message,
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                    toast.addEventListener('click', () => {
                        if (notification.url && notification.url !== '#') window.location.href = notification.url;
                    })
                }
            });
        } else {
            // Fallback custom toast
            let toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;background:white;padding:15px;min-width:250px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;border-left:4px solid #7367F0;cursor:pointer;animation:slideIn 0.3s ease-out;font-family:inherit;';
            toast.innerHTML = `<div style="font-weight:bold;margin-bottom:4px;color:#333;">${notification.title}</div><div style="font-size:0.9em;color:#666;">${notification.message}</div>`;

            // Add slide-in animation style
            if (!document.getElementById('toast-style')) {
                const style = document.createElement('style');
                style.id = 'toast-style';
                style.innerHTML = '@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
                document.head.appendChild(style);
            }

            toast.onclick = () => { if (notification.url && notification.url !== '#') window.location.href = notification.url; };
            document.body.appendChild(toast);

            // Play sound? Optional.

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    }

    // Check immediately to set baseline, then poll
    checkNotifications(false);

    // Poll every 10 seconds
    setInterval(() => checkNotifications(true), 10000);
});
