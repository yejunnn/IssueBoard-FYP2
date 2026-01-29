@extends('layouts.app')

@section('title', 'Real-time Test')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Real-time AJAX Test
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This page tests the AJAX-based real-time update system. Click the buttons below to simulate different types of updates.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Notification Tests</h5>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" onclick="testNotification('New Ticket Created', 'A new ticket has been submitted')">
                                    <i class="fas fa-bell me-2"></i>Test Notification
                                </button>
                                <button class="btn btn-success" onclick="testNotification('Ticket Updated', 'Ticket status has been changed')">
                                    <i class="fas fa-check me-2"></i>Test Success Notification
                                </button>
                                <button class="btn btn-warning" onclick="testNotification('Warning', 'This is a warning message')">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Test Warning
                                </button>
                                <button class="btn btn-info" onclick="createTestNotification()">
                                    <i class="fas fa-plus me-2"></i>Create Real Notification
                                </button>
                                <button class="btn btn-danger" onclick="clearNotifications()">
                                    <i class="fas fa-trash me-2"></i>Clear All Notifications
                                </button>
                                <button class="btn btn-warning" onclick="testNotificationSystem()">
                                    <i class="fas fa-cogs me-2"></i>Test Complete System
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Real-time Status</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="realtime-indicator" id="connectionStatus"></div>
                                        <span class="ms-2" id="statusText">Checking connection...</span>
                                    </div>
                                    <div class="small text-muted">
                                        <div>Last Update: <span id="lastUpdate">Never</span></div>
                                        <div>Update Count: <span id="updateCount">0</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Live Updates Log</h5>
                            <div class="card bg-dark text-light">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <div id="updateLog" class="small">
                                        <div class="text-muted">Waiting for updates...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>AJAX Test Results</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h4 id="totalTickets">0</h4>
                                            <small>Total Tickets</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h4 id="openTickets">0</h4>
                                            <small>Open Tickets</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h4 id="inProgressTickets">0</h4>
                                            <small>In Progress</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h4 id="completedTickets">0</h4>
                                            <small>Completed</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let updateCount = 0;

function testNotification(title, message) {
    // Simulate a real-time notification
    if (typeof showToast === 'function') {
        showToast(title, message, 'info');
    }
    
    // Update the log
    addToLog(`Notification: ${title} - ${message}`);
    
    // Update counter
    updateCount++;
    document.getElementById('updateCount').textContent = updateCount;
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
}

function createTestNotification() {
    fetch('/test-notification', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addToLog(`Real notification created: ${data.message}`);
            showToast('Success', 'Test notification created successfully!', 'success');
            
            // Update notification count
            setTimeout(() => {
                testAjaxUpdates();
            }, 1000);
        } else {
            addToLog(`Error: ${data.message}`);
            showToast('Error', 'Failed to create notification', 'danger');
        }
    })
    .catch(error => {
        addToLog(`Error: ${error.message}`);
        showToast('Error', 'Failed to create notification', 'danger');
    });
}

function clearNotifications() {
    fetch('/test-clear-notifications', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addToLog(`Notifications cleared: ${data.message}`);
            showToast('Success', 'All notifications cleared!', 'success');
            
            // Update notification count
            setTimeout(() => {
                testAjaxUpdates();
            }, 1000);
        } else {
            addToLog(`Error: ${data.message}`);
            showToast('Error', 'Failed to clear notifications', 'danger');
        }
    })
    .catch(error => {
        addToLog(`Error: ${error.message}`);
        showToast('Error', 'Failed to clear notifications', 'danger');
    });
}

function testNotificationSystem() {
    fetch('/test-notification-system', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addToLog(`System test completed: ${data.message}`);
            showToast('Success', `Created ${data.notifications_created} test notifications!`, 'success');
            
            // Update notification count
            setTimeout(() => {
                testAjaxUpdates();
            }, 1000);
        } else {
            addToLog(`Error: ${data.message}`);
            showToast('Error', 'Failed to test notification system', 'danger');
        }
    })
    .catch(error => {
        addToLog(`Error: ${error.message}`);
        showToast('Error', 'Failed to test notification system', 'danger');
    });
}

function addToLog(message) {
    const log = document.getElementById('updateLog');
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = document.createElement('div');
    logEntry.innerHTML = `<span class="text-info">[${timestamp}]</span> ${message}`;
    log.appendChild(logEntry);
    
    // Keep only last 20 entries
    while (log.children.length > 20) {
        log.removeChild(log.firstChild);
    }
    
    // Auto-scroll to bottom
    log.scrollTop = log.scrollHeight;
}

// Test AJAX updates
function testAjaxUpdates() {
    // Test notification count
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            addToLog(`AJAX: Notification count updated to ${data.count}`);
        })
        .catch(error => {
            addToLog(`AJAX Error: ${error.message}`);
        });

    // Test statistics (if admin)
    if (document.querySelector('meta[name="user-role"]')?.getAttribute('content') === 'admin') {
        fetch('/admin/statistics')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalTickets').textContent = data.total || 0;
                document.getElementById('openTickets').textContent = data.open || 0;
                document.getElementById('inProgressTickets').textContent = data.in_progress || 0;
                document.getElementById('completedTickets').textContent = data.completed || 0;
                addToLog(`AJAX: Statistics updated - Total: ${data.total}, Open: ${data.open}`);
            })
            .catch(error => {
                addToLog(`AJAX Error: ${error.message}`);
            });
    }
}

// Check real-time connection status
function checkConnectionStatus() {
    const statusIndicator = document.getElementById('connectionStatus');
    const statusText = document.getElementById('statusText');
    
    if (window.Echo && window.Echo.connector.pusher.connection.state === 'connected') {
        statusIndicator.className = 'realtime-indicator';
        statusText.textContent = 'Real-time Connected';
        addToLog('Status: Real-time connection active');
    } else {
        statusIndicator.className = 'realtime-indicator disconnected';
        statusText.textContent = 'AJAX Fallback Mode';
        addToLog('Status: Using AJAX fallback');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    checkConnectionStatus();
    
    // Test AJAX updates every 10 seconds
    setInterval(() => {
        testAjaxUpdates();
    }, 10000);
    
    // Check connection status every 30 seconds
    setInterval(() => {
        checkConnectionStatus();
    }, 30000);
    
    addToLog('Real-time test page loaded');
});
</script>
@endsection 