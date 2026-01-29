export class RealTimeAJAX {
    constructor() {
        this.updateInterval = 5000;
        this.isUpdating = false;
        this.lastUpdate = Date.now();
    }

    startUpdates() {
        setInterval(() => {
            this.performUpdates();
        }, this.updateInterval);
    }

    async performUpdates() {
        if (this.isUpdating) return;
        
        this.isUpdating = true;
        
        try {
            await Promise.all([
                this.updateNotifications(),
                this.updateTickets(),
                this.updateStatistics()
            ]);
        } catch (error) {
            console.error('AJAX update error:', error);
        } finally {
            this.isUpdating = false;
            this.lastUpdate = Date.now();
        }
    }

    async updateNotifications() {
        try {
            const response = await fetch('/notifications/unread-count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateNotificationBadge(data.count);
            }
        } catch (error) {
            console.error('Failed to update notifications:', error);
        }
    }

    async updateTickets() {
        const ticketsContainer = document.querySelector('.tickets-container');
        if (!ticketsContainer) return;

        try {
            const currentUrl = new URL(window.location);
            const response = await fetch(currentUrl.pathname + currentUrl.search, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (response.ok) {
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTicketsContainer = doc.querySelector('.tickets-container');
                
                if (newTicketsContainer) {
                    this.updateTicketList(ticketsContainer, newTicketsContainer);
                }
            }
        } catch (error) {
            console.error('Failed to update tickets:', error);
        }
    }

    async updateStatistics() {
        try {
            const response = await fetch('/admin/statistics', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateStatisticsDisplay(data);
            }
        } catch (error) {
            console.error('Failed to update statistics:', error);
        }
    }

    updateNotificationBadge(count) {
        const notificationLink = document.querySelector('a[href*="notifications"]');
        if (!notificationLink) return;

        let badge = notificationLink.querySelector('.notification-badge');
        
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'notification-badge position-absolute top-0 end-0 bg-danger text-white small fw-bold rounded-circle d-flex align-items-center justify-content-center';
                badge.style.cssText = 'min-width: 18px; height: 18px; font-size: 0.75rem;';
                notificationLink.appendChild(badge);
            }
            
            const currentCount = parseInt(badge.textContent.replace('+', '')) || 0;
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
            
            if (count > currentCount) {
                badge.classList.add('pulse-animation');
                setTimeout(() => {
                    badge.classList.remove('pulse-animation');
                }, 2000);
            }
        } else {
            if (badge) {
                badge.remove();
            }
        }
    }

    updateTicketList(currentContainer, newContainer) {
        const currentTickets = Array.from(currentContainer.querySelectorAll('tr[data-ticket-id]'));
        const newTickets = Array.from(newContainer.querySelectorAll('tr[data-ticket-id]'));
        
        const currentIds = currentTickets.map(row => row.getAttribute('data-ticket-id'));
        const newIds = newTickets.map(row => row.getAttribute('data-ticket-id'));
        
        const addedTickets = newIds.filter(id => !currentIds.includes(id));
        
        addedTickets.forEach(ticketId => {
            const newTicketRow = newContainer.querySelector(`tr[data-ticket-id="${ticketId}"]`);
            if (newTicketRow) {
                currentContainer.insertAdjacentHTML('afterbegin', newTicketRow.outerHTML);
                const addedRow = currentContainer.querySelector(`tr[data-ticket-id="${ticketId}"]`);
                addedRow.classList.add('highlight-new');
                setTimeout(() => {
                    addedRow.classList.remove('highlight-new');
                }, 3000);
            }
        });
    }

    updateStatisticsDisplay(data) {
        const elements = {
            total: document.querySelector('.card.bg-primary h4'),
            open: document.querySelector('.card.bg-warning h4'),
            inProgress: document.querySelector('.card.bg-info h4'),
            completed: document.querySelector('.card.bg-success h4'),
            cancelled: document.querySelector('.card.bg-danger h4')
        };

        if (elements.total && data.total !== undefined) {
            elements.total.textContent = data.total;
        }
        if (elements.open && data.open !== undefined) {
            elements.open.textContent = data.open;
        }
        if (elements.inProgress && data.in_progress !== undefined) {
            elements.inProgress.textContent = data.in_progress;
        }
        if (elements.completed && data.completed !== undefined) {
            elements.completed.textContent = data.completed;
        }
        if (elements.cancelled && data.cancel !== undefined) {
            elements.cancelled.textContent = data.cancel;
        }
    }
}

// Auto-initialize if not imported as module
if (typeof window !== 'undefined') {
document.addEventListener('DOMContentLoaded', function() {
    const ajaxUpdates = new RealTimeAJAX();
    ajaxUpdates.startUpdates();
}); 
} 