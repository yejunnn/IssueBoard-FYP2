<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('department.{departmentId}', function ($user, $departmentId) {
    return $user->department_id == $departmentId;
});

Broadcast::channel('admin.tickets', function ($user) {
    return $user->is_admin;
});

Broadcast::channel('tickets', function ($user) {
    return true;
}); 