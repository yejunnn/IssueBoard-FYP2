<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/test-realtime', function () {
    return view('test-realtime');
})->name('test.realtime');

Route::post('/test-notification', [\App\Http\Controllers\TestController::class, 'testNotification'])->name('test.notification');
Route::post('/test-ticket', [\App\Http\Controllers\TestController::class, 'testTicket'])->name('test.ticket');
Route::post('/test-clear-notifications', [\App\Http\Controllers\TestController::class, 'clearNotifications'])->name('test.clear-notifications');
Route::get('/test-notification-count', [\App\Http\Controllers\TestController::class, 'getNotificationCount'])->name('test.notification-count');
Route::post('/test-notification-system', [\App\Http\Controllers\TestController::class, 'testNotificationSystem'])->name('test.notification-system');
Route::get('/test-create-notification', [\App\Http\Controllers\TestController::class, 'createSimpleNotification'])->name('test.create-simple-notification');

Route::get('tickets/assigned', [TicketController::class, 'assigned'])->name('tickets.assigned')->middleware('auth');

Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

Route::post('tickets/{ticket}/acknowledge', [TicketController::class, 'acknowledge'])->name('tickets.acknowledge');
Route::post('/tickets/{ticket}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel')->middleware('auth');
Route::post('/tickets/{ticket}/restore', [TicketController::class, 'restore'])->name('tickets.restore')->middleware('auth');
Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('tickets', TicketController::class)->except(['create', 'store', 'index', 'show']);
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/tickets/bulk-status', [AdminController::class, 'bulkUpdateStatus'])->name('admin.tickets.bulk-status');
    Route::post('/admin/tickets/bulk-cancel', [AdminController::class, 'bulkCancelTickets'])->name('admin.tickets.bulk-cancel');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::post('/cleanup-cancelled-tickets', [AdminController::class, 'cleanupCancelledTickets'])->name('cleanup-cancelled-tickets');
    Route::get('/tickets/{ticket}/assign', [AdminController::class, 'showAssignTicket'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/assign', [AdminController::class, 'assignTicket'])->name('tickets.assign.store');
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'delete'])->name('notifications.delete');
    Route::delete('/notifications', [\App\Http\Controllers\NotificationController::class, 'deleteAll'])->name('notifications.delete-all');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'department'])->prefix('department')->name('department.')->group(function () {
    Route::redirect('/', '/department/dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\DepartmentController::class, 'dashboard'])->name('dashboard');
    Route::post('/tickets/{ticket_id}/accept', [\App\Http\Controllers\TicketController::class, 'accept'])->name('tickets.accept');
});



require __DIR__.'/auth.php';
