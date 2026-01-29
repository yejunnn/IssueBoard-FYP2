<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;

class NotificationService
{
    public static function ticketAssigned(Ticket $ticket, User $assignedUser)
    {
        $notification = Notification::create([
            'user_id' => $assignedUser->id,
            'type' => 'ticket_assigned',
            'title' => 'New Ticket Assigned',
            'message' => "You have been assigned ticket #{$ticket->id}: {$ticket->name}",
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_name' => $ticket->name,
                'assigned_by' => auth()->user()->name,
            ],
        ]);
        
        try {
            event(new NotificationCreated($notification));
        } catch (\Exception $e) {
            \Log::error('Broadcasting failed for ticket assignment: ' . $e->getMessage());
        }

        $adminUsers = User::where('is_admin', true)->get();
        
        foreach ($adminUsers as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'type' => 'ticket_assigned_admin',
                'title' => 'Ticket Assigned',
                'message' => "Ticket #{$ticket->id}: {$ticket->name} has been assigned to {$assignedUser->name}",
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    'assigned_to' => $assignedUser->name,
                    'assigned_by' => auth()->user()->name,
                ],
            ]);
            
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for admin assignment notification: ' . $e->getMessage());
            }
        }
    }

    public static function ticketReassigned(Ticket $ticket, User $newAssignee, User $previousAssignee)
    {
        $notification1 = Notification::create([
            'user_id' => $newAssignee->id,
            'type' => 'ticket_reassigned',
            'title' => 'Ticket Reassigned to You',
            'message' => "Ticket #{$ticket->id}: {$ticket->name} has been reassigned to you from {$previousAssignee->name}",
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_name' => $ticket->name,
                'assigned_by' => auth()->user()->name,
                'previous_assignee' => $previousAssignee->name,
            ],
        ]);

        $notification2 = Notification::create([
            'user_id' => $previousAssignee->id,
            'type' => 'ticket_unassigned',
            'title' => 'Ticket Unassigned',
            'message' => "Ticket #{$ticket->id}: {$ticket->name} has been reassigned from you to {$newAssignee->name}",
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_name' => $ticket->name,
                'reassigned_by' => auth()->user()->name,
                'new_assignee' => $newAssignee->name,
            ],
        ]);
        
        try {
            event(new NotificationCreated($notification1));
            event(new NotificationCreated($notification2));
        } catch (\Exception $e) {
        }
    }

    public static function ticketAccepted(Ticket $ticket, User $acceptedBy)
    {
        $adminUsers = User::where('is_admin', true)->get();
        
        foreach ($adminUsers as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'type' => 'ticket_accepted',
                'title' => 'Ticket Accepted',
                'message' => "User {$acceptedBy->name} accepted ticket #{$ticket->id}: {$ticket->name}",
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    'accepted_by' => $acceptedBy->name,
                    'accepted_by_id' => $acceptedBy->id,
                ],
            ]);
            
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for ticket acceptance notification: ' . $e->getMessage());
            }
        }

        if ($ticket->department_id) {
            $departmentUsers = User::where('department_id', $ticket->department_id)
                ->where('is_admin', false)
                ->where('id', '!=', $acceptedBy->id)
                ->get();
            
            foreach ($departmentUsers as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => 'ticket_accepted_department',
                    'title' => 'Ticket Accepted in Your Department',
                    'message' => "Ticket #{$ticket->id}: {$ticket->name} has been accepted by {$acceptedBy->name}",
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'ticket_name' => $ticket->name,
                        'accepted_by' => $acceptedBy->name,
                    ],
                ]);
                
                try {
                    event(new NotificationCreated($notification));
                } catch (\Exception $e) {
                    \Log::error('Broadcasting failed for department acceptance notification: ' . $e->getMessage());
                }
            }
        }
    }

    public static function ticketStatusUpdated(Ticket $ticket, User $updatedBy, string $oldStatus, string $newStatus)
    {
        $adminUsers = User::where('is_admin', true)->get();
        
        foreach ($adminUsers as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'type' => 'ticket_status_updated',
                'title' => 'Ticket Status Updated',
                'message' => "Ticket #{$ticket->id} status changed from {$oldStatus} to {$newStatus} by {$updatedBy->name}",
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    'updated_by' => $updatedBy->name,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ],
            ]);
            
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for status update notification: ' . $e->getMessage());
            }
        }

        if ($ticket->department_id) {
            $departmentUsers = User::where('department_id', $ticket->department_id)
                ->where('is_admin', false)
                ->get();
            
            foreach ($departmentUsers as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => 'ticket_status_updated_department',
                    'title' => 'Ticket Status Updated in Your Department',
                    'message' => "Ticket #{$ticket->id} status changed from {$oldStatus} to {$newStatus}",
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'ticket_name' => $ticket->name,
                        'updated_by' => $updatedBy->name,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                    ],
                ]);
                
                try {
                    event(new NotificationCreated($notification));
                } catch (\Exception $e) {
                    \Log::error('Broadcasting failed for department status notification: ' . $e->getMessage());
                }
            }
        }
    }

    public static function ticketAcknowledged(Ticket $ticket, User $acknowledgedBy)
    {
        $adminUsers = User::where('is_admin', true)->get();
        
        foreach ($adminUsers as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'type' => 'ticket_acknowledged',
                'title' => 'Ticket Acknowledged',
                'message' => "Ticket #{$ticket->id} was acknowledged by {$acknowledgedBy->name}",
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    'acknowledged_by' => $acknowledgedBy->name,
                ],
            ]);
            
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for ticket acknowledgment notification: ' . $e->getMessage());
            }
        }

        if ($ticket->department_id) {
            $departmentUsers = User::where('department_id', $ticket->department_id)
                ->where('is_admin', false)
                ->where('id', '!=', $acknowledgedBy->id)
                ->get();
            
            foreach ($departmentUsers as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => 'ticket_acknowledged_department',
                    'title' => 'Ticket Acknowledged in Your Department',
                    'message' => "Ticket #{$ticket->id}: {$ticket->name} has been acknowledged by {$acknowledgedBy->name}",
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'ticket_name' => $ticket->name,
                        'acknowledged_by' => $acknowledgedBy->name,
                    ],
                ]);
                
                try {
                    event(new NotificationCreated($notification));
                } catch (\Exception $e) {
                    \Log::error('Broadcasting failed for department acknowledgment notification: ' . $e->getMessage());
                }
            }
        }
    }

    public static function newTicketCreated(Ticket $ticket)
    {
        $adminUsers = User::where('is_admin', true)->get();
        
        foreach ($adminUsers as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'type' => 'new_ticket',
                'title' => 'New Ticket Created',
                'message' => "New ticket #{$ticket->id}: {$ticket->name} has been created in {$ticket->department->name}",
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    'department' => $ticket->department->name,
                ],
            ]);
            
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for new ticket notification: ' . $e->getMessage());
            }
        }

        if ($ticket->department_id) {
            $departmentUsers = User::where('department_id', $ticket->department_id)
                ->where('is_admin', false)
                ->get();
            
            foreach ($departmentUsers as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => 'new_ticket_department',
                    'title' => 'New Ticket in Your Department',
                    'message' => "New ticket #{$ticket->id}: {$ticket->name} has been created in your department",
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'ticket_name' => $ticket->name,
                        'department' => $ticket->department->name,
                    ],
                ]);
                
                try {
                    event(new NotificationCreated($notification));
                } catch (\Exception $e) {
                    \Log::error('Broadcasting failed for department ticket notification: ' . $e->getMessage());
                }
            }
        }
    }
} 