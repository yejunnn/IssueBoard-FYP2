import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

function escapeHtml(text) {
    if (typeof text !== 'string') return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function sanitizeUrl(url) {
    if (!url || typeof url !== 'string') return '';
    try {
        const urlObj = new URL(url, window.location.origin);
        return urlObj.href;
    } catch {
        return '';
    }
}

if (import.meta.env.VITE_PUSHER_APP_KEY && import.meta.env.VITE_PUSHER_APP_KEY !== 'your-pusher-app-key') {
    console.log('Initializing Pusher with key:', import.meta.env.VITE_PUSHER_APP_KEY);
    
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        enableLogging: false,
        activityTimeout: isMobile ? 30000 : 120000,
        pongTimeout: isMobile ? 10000 : 30000,
    });
    
    console.log('Pusher initialized successfully!');
    console.log('Mobile device detected:', isMobile);
    
    if (isMobile) {
        console.log('Mobile optimizations enabled');
        
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('Page hidden - connection may be paused');
            } else {
                console.log('Page visible - connection resumed');
            }
        });
        
        window.addEventListener('online', () => {
            console.log('Network online - reconnecting...');
        });
        
        window.addEventListener('offline', () => {
            console.log('Network offline - connection paused');
        });
    }
} else {
    console.log('Pusher not configured. Real-time features disabled.');
    console.log('Available env vars:', {
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER
    });
    window.Echo = null;
}

document.addEventListener('DOMContentLoaded', function() {
    const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
    const isAuthenticated = document.querySelector('meta[name="authenticated"]')?.getAttribute('content') === 'true';
    const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
    const userDepartment = document.querySelector('meta[name="user-department"]')?.getAttribute('content');

    initializeRealTimeFeatures(userId, isAuthenticated, userRole, userDepartment);
    
    if (isAuthenticated) {
        setInterval(() => {
            updateNotificationCount();
            updateTicketStatistics();
        }, 30000);
    }
});

function initializeRealTimeFeatures(userId, isAuthenticated, userRole, userDepartment) {
    if (isAuthenticated && userId && window.Echo) {
        console.log('Setting up real-time features for user:', userId, 'Role:', userRole);
        
        window.Echo.private(`notifications.${userId}`)
            .listen('.notification.created', (e) => {
                console.log('Received notification:', e.notification);
                handleNewNotification(e.notification);
                updateNotificationCount();
                updateNotificationBadge();
                showRealTimeAlert('New Notification', e.notification.title, 'info');
            })
            .listen('.notification.read', (e) => {
                console.log('Notification marked as read:', e.notification);
                updateNotificationCount();
            });

        if (userDepartment && userRole !== 'admin') {
            window.Echo.private(`department.${userDepartment}`)
                .listen('.ticket.assigned', (e) => {
                    console.log('Ticket assigned to department:', e.ticket);
                    handleTicketAssignment(e.ticket);
                    showRealTimeAlert('New Ticket Assigned', `Ticket #${e.ticket.id}: ${e.ticket.name}`, 'success');
                    updateTicketStatistics();
                })
                .listen('.ticket.status.updated', (e) => {
                    console.log('Ticket status updated in department:', e.ticket);
                    handleTicketStatusUpdate(e.ticket);
                    showRealTimeAlert('Ticket Status Updated', `Ticket #${e.ticket.id} status: ${e.ticket.status}`, 'warning');
                    updateTicketStatistics();
                })
                .listen('.ticket.accepted', (e) => {
                    console.log('Ticket accepted in department:', e.ticket);
                    handleTicketAcceptance(e.ticket);
                    showRealTimeAlert('Ticket Accepted', `Ticket #${e.ticket.id} accepted`, 'success');
                    updateTicketStatistics();
                });
        }

        if (userRole === 'admin') {
            window.Echo.channel('admin.tickets')
                .listen('.ticket.created', (e) => {
                    console.log('New ticket created (admin):', e.ticket);
                    handleNewTicket(e.ticket);
                    updateTicketStatistics();
                    showRealTimeAlert('New Ticket Created', `Ticket #${e.ticket.id}: ${e.ticket.name}`, 'success');
                })
                .listen('.ticket.status.updated', (e) => {
                    console.log('Ticket status updated (admin):', e.ticket);
                    handleTicketStatusUpdate(e.ticket);
                    updateTicketStatistics();
                    showRealTimeAlert('Ticket Status Updated', `Ticket #${e.ticket.id} status: ${e.ticket.status}`, 'warning');
                })
                .listen('.ticket.assigned', (e) => {
                    console.log('Ticket assigned (admin):', e.ticket);
                    handleTicketAssignment(e.ticket);
                    showRealTimeAlert('Ticket Assigned', `Ticket #${e.ticket.id} to ${e.ticket.assigned_to}`, 'info');
                })
                .listen('.ticket.accepted', (e) => {
                    console.log('Ticket accepted (admin):', e.ticket);
                    handleTicketAcceptance(e.ticket);
                    showRealTimeAlert('Ticket Accepted', `Ticket #${e.ticket.id} by ${e.ticket.accepted_by}`, 'success');
                })
                .listen('.ticket.acknowledged', (e) => {
                    console.log('Ticket acknowledged (admin):', e.ticket);
                    handleTicketAcknowledgment(e.ticket);
                    showRealTimeAlert('Ticket Acknowledged', `Ticket #${e.ticket.id} acknowledged`, 'success');
                });
        }

        window.Echo.channel('tickets')
            .listen('.ticket.created', (e) => {
                console.log('Global ticket created:', e.ticket);
                handleNewTicket(e.ticket);
                updateTicketStatistics();
            })
            .listen('.ticket.status.updated', (e) => {
                console.log('Global ticket status updated:', e.ticket);
                handleTicketStatusUpdate(e.ticket);
                updateTicketStatistics();
            });
    } else {
        console.log('Real-time features not available, using AJAX fallback');
        if (isAuthenticated) {
            setInterval(() => {
                updateNotificationCount();
                updateTicketStatistics();
            }, 10000);
        }
    }
}

function handleNewNotification(notification) {
    const notificationLink = document.querySelector('a[href*="notifications"]');
    if (!notificationLink) return;

    let badge = notificationLink.querySelector('.notification-badge');
    const currentCount = parseInt(badge?.textContent.replace('+', '') || '0');
    
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'notification-badge position-absolute top-0 end-0 bg-danger text-white small fw-bold rounded-circle d-flex align-items-center justify-content-center';
        badge.style.cssText = 'min-width: 18px; height: 18px; font-size: 0.75rem;';
        notificationLink.appendChild(badge);
    }
    
    badge.textContent = (currentCount + 1) > 99 ? '99+' : (currentCount + 1);
    badge.style.display = 'flex';
    
    badge.classList.add('pulse-animation');
    setTimeout(() => {
        badge.classList.remove('pulse-animation');
    }, 2000);

    showToast(escapeHtml(notification.title), escapeHtml(notification.message), 'info');
}

function handleNewTicket(ticket) {
    const ticketsContainer = document.querySelector('.tickets-container');
    if (ticketsContainer) {
        const isTable = ticketsContainer.tagName === 'TBODY';
        
        if (isTable) {
            const ticketHtml = createTicketTableRow(ticket);
            ticketsContainer.insertAdjacentHTML('afterbegin', ticketHtml);
            
            const newRow = ticketsContainer.querySelector(`[data-ticket-id="${ticket.id}"]`);
            if (newRow) {
                newRow.classList.add('highlight-new');
                setTimeout(() => {
                    newRow.classList.remove('highlight-new');
                }, 3000);
            }
        } else {
            const ticketHtml = createTicketCard(ticket);
            ticketsContainer.insertAdjacentHTML('afterbegin', ticketHtml);
        }
        
        showToast('New Ticket Created', `Ticket #${ticket.id}: ${escapeHtml(ticket.name)}`, 'success');
        updateTicketStatistics();
    }
}

function handleTicketStatusUpdate(ticket) {
    const ticketElement = document.querySelector(`[data-ticket-id="${ticket.id}"]`);
    if (ticketElement) {
        const statusBadge = ticketElement.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.textContent = ticket.status.replace('_', ' ').toUpperCase();
            statusBadge.className = `badge status-badge bg-${getStatusColor(ticket.status)}`;
        }
        
        ticketElement.classList.add('highlight-update');
        setTimeout(() => {
            ticketElement.classList.remove('highlight-update');
        }, 3000);
    }
}

function handleTicketAssignment(ticket) {
    const ticketElement = document.querySelector(`[data-ticket-id="${ticket.id}"]`);
    if (ticketElement) {
        const assignmentInfo = ticketElement.querySelector('.assignment-info');
        if (assignmentInfo) {
            assignmentInfo.innerHTML = `<i class="fas fa-user-check"></i> ${escapeHtml(ticket.assigned_to)}`;
        }
        
        ticketElement.classList.add('highlight-assignment');
        setTimeout(() => {
            ticketElement.classList.remove('highlight-assignment');
        }, 3000);
    }
}

function handleTicketAcceptance(ticket) {
    const ticketElement = document.querySelector(`[data-ticket-id="${ticket.id}"]`);
    if (ticketElement) {
        const acceptanceInfo = ticketElement.querySelector('.acceptance-info');
        if (acceptanceInfo) {
            acceptanceInfo.innerHTML = `<i class="fas fa-check-circle"></i> ${escapeHtml(ticket.accepted_by)}`;
        }
        
        ticketElement.classList.add('highlight-acceptance');
        setTimeout(() => {
            ticketElement.classList.remove('highlight-acceptance');
        }, 3000);
    }
}

function handleTicketAcknowledgment(ticket) {
    const ticketElement = document.querySelector(`[data-ticket-id="${ticket.id}"]`);
    if (ticketElement) {
        const ackCell = ticketElement.querySelector('td:nth-child(7)');
        if (ackCell) {
            ackCell.innerHTML = `<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>1</span>`;
        }
        
        ticketElement.classList.add('highlight-acknowledgment');
        setTimeout(() => {
            ticketElement.classList.remove('highlight-acknowledgment');
        }, 3000);
    }
}

function createTicketCard(ticket) {
    const statusColor = getStatusColor(ticket.status);
    const imagePath = ticket.image_path ? sanitizeUrl(`/storage/${ticket.image_path}`) : '/images/placeholder.png';
    
    return `
        <div class="col-md-6 col-lg-4 mb-4" data-ticket-id="${ticket.id}">
            <div class="card h-100 shadow-sm">
                <img src="${imagePath}" class="card-img-top" alt="Ticket Image" style="height: 200px; object-fit: cover;" loading="lazy">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">${escapeHtml(ticket.name)}</h5>
                        <div>
                            <span class="badge bg-${statusColor} status-badge">
                                ${escapeHtml(ticket.status.replace('_', ' ').toUpperCase())}
                            </span>
                        </div>
                    </div>
                    
                    <p class="card-text text-muted small">
                        <i class="fas fa-map-marker-alt me-1"></i>${escapeHtml(ticket.location)}
                    </p>
                    
                    <p class="card-text">${escapeHtml(ticket.description.substring(0, 100))}${ticket.description.length > 100 ? '...' : ''}</p>
                    
                    <div class="row text-muted small mb-2">
                        <div class="col-6">
                            <i class="fas fa-tag me-1"></i>${escapeHtml(ticket.category)}
                        </div>
                        <div class="col-6">
                            <i class="fas fa-building me-1"></i>${escapeHtml(ticket.department)}
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>${escapeHtml(ticket.created_at)}
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between">
                        <a href="/tickets/${ticket.id}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function createTicketTableRow(ticket) {
    const statusColor = getStatusColor(ticket.status);
    const imageIcon = ticket.image_path ? '<i class="fas fa-image text-info ms-1" title="Has Image"></i>' : '';
    const assignButton = (ticket.status === 'open' || ticket.status === 'submitted') ? 
        `<a href="/admin/tickets/${ticket.id}/assign" class="btn btn-sm btn-outline-success" title="Assign to User">
            <i class="fas fa-user-plus"></i>
        </a>` : '';
    
    return `
        <tr data-ticket-id="${ticket.id}">
            <td>#${ticket.id}</td>
            <td>
                <strong>${escapeHtml(ticket.name)}</strong>
                ${imageIcon}
            </td>
            <td>
                <span class="badge bg-secondary">${escapeHtml(ticket.category)}</span>
            </td>
            <td>
                <span class="badge bg-info">${escapeHtml(ticket.department)}</span>
            </td>
            <td>
                <span class="badge bg-${statusColor} status-badge">${escapeHtml(ticket.status.replace('_', ' ').toUpperCase())}</span>
            </td>
            <td>${escapeHtml(ticket.created_at)}</td>
            <td>
                <div class="btn-group" role="group">
                    <a href="/tickets/${ticket.id}" class="btn btn-sm btn-outline-primary" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/tickets/${ticket.id}/edit" class="btn btn-sm btn-outline-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    ${assignButton}
                </div>
            </td>
        </tr>
    `;
}

function getStatusColor(status) {
    switch (status) {
        case 'open': return 'info';
        case 'in_progress': return 'warning';
        case 'completed': return 'success';
        case 'cancel': return 'danger';
        case 'submitted': return 'info';
        default: return 'secondary';
    }
}

function updateNotificationCount() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const notificationLink = document.querySelector('a[href*="notifications"]');
            if (!notificationLink) return;

            let badge = notificationLink.querySelector('.notification-badge');
            
            if (data.count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'notification-badge position-absolute top-0 end-0 bg-danger text-white small fw-bold rounded-circle d-flex align-items-center justify-content-center';
                    badge.style.cssText = 'min-width: 18px; height: 18px; font-size: 0.75rem;';
                    notificationLink.appendChild(badge);
                }
                
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'flex';
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        })
        .catch(error => {
            console.error('Error updating notification count:', error);
        });
}

function updateTicketStatistics() {
    const totalElement = document.querySelector('.card.bg-primary h4');
    const openElement = document.querySelector('.card.bg-warning h4');
    const completedElement = document.querySelector('.card.bg-success h4');
    const pendingElement = document.querySelector('.card.bg-info h4');
    
    if (totalElement) {
        const currentTotal = parseInt(totalElement.textContent) || 0;
        totalElement.textContent = currentTotal + 1;
    }
    
    if (openElement && document.querySelector('.badge.bg-info')) {
        const currentOpen = parseInt(openElement.textContent) || 0;
        openElement.textContent = currentOpen + 1;
    }
    
    updateDepartmentStatistics();
}

function updateDepartmentStatistics() {
    const departmentRows = document.querySelectorAll('tbody tr[data-department-id]');
    departmentRows.forEach(row => {
        const ticketCountElement = row.querySelector('.badge.bg-primary');
        if (ticketCountElement) {
            const currentCount = parseInt(ticketCountElement.textContent) || 0;
            ticketCountElement.textContent = currentCount + 1;
        }
    });
}

function updateNotificationBadge() {
    const notificationLink = document.querySelector('a[href*="notifications"]');
    if (!notificationLink) return;

    let badge = notificationLink.querySelector('.notification-badge');
    const currentCount = parseInt(badge?.textContent.replace('+', '') || '0');
    
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'notification-badge position-absolute top-0 end-0 bg-danger text-white small fw-bold rounded-circle d-flex align-items-center justify-content-center';
        badge.style.cssText = 'min-width: 18px; height: 18px; font-size: 0.75rem;';
        notificationLink.appendChild(badge);
    }
    
    badge.textContent = (currentCount + 1) > 99 ? '99+' : (currentCount + 1);
    badge.style.display = 'flex';
    
    badge.classList.add('pulse-animation');
    setTimeout(() => {
        badge.classList.remove('pulse-animation');
    }, 2000);
}

function showToast(title, message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${escapeHtml(title)}</strong><br>
                ${escapeHtml(message)}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

function showRealTimeAlert(title, message, type = 'info') {
    showToast(title, message, type);
    
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: message,
            icon: '/images/logo.png',
            badge: '/images/logo.png'
        });
    }
    
    playNotificationSound();
}

function playNotificationSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
    oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
    
    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.2);
}

if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
} 