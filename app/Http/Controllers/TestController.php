<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Ticket;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function testNotification(Request $request)
    {
        $user = Auth::user();
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification to verify real-time updates.',
            'type' => 'info',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test notification created',
            'notification' => $notification
        ]);
    }

    public function testTicket(Request $request)
    {
        $ticket = Ticket::create([
            'name' => 'Test Ticket - ' . now()->format('H:i:s'),
            'description' => 'This is a test ticket to verify real-time updates.',
            'category_id' => 1,
            'department_id' => 1,
            'location' => 'Test Location',
            'status' => 'open',
            'created_by' => Auth::id(),
        ]);

        NotificationService::newTicketCreated($ticket);

        return response()->json([
            'success' => true,
            'message' => 'Test ticket created',
            'ticket' => $ticket
        ]);
    }

    public function clearNotifications()
    {
        $user = Auth::user();
        $user->notifications()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared'
        ]);
    }

    public function getNotificationCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }

    public function testNotificationSystem()
    {
        $user = Auth::user();
        
        $notifications = [
            [
                'title' => 'System Test - New Ticket',
                'message' => 'A new ticket has been created in your department',
                'type' => 'new_ticket_department'
            ],
            [
                'title' => 'System Test - Ticket Assigned',
                'message' => 'You have been assigned a new ticket',
                'type' => 'ticket_assigned'
            ],
            [
                'title' => 'System Test - Status Update',
                'message' => 'A ticket status has been updated',
                'type' => 'ticket_status_updated'
            ],
            [
                'title' => 'System Test - Ticket Accepted',
                'message' => 'A ticket has been accepted in your department',
                'type' => 'ticket_accepted_department'
            ]
        ];
        
        $createdCount = 0;
        foreach ($notifications as $notificationData) {
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'type' => $notificationData['type'],
                'is_read' => false,
            ]);
            
            try {
                event(new \App\Events\NotificationCreated($notification));
                $createdCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to broadcast notification: ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Created {$createdCount} test notifications",
            'notifications_created' => $createdCount
        ]);
    }

    public function createSimpleNotification()
    {
        $user = Auth::user();
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification to verify the system is working',
            'type' => 'test',
            'is_read' => false,
        ]);
        
        try {
            event(new \App\Events\NotificationCreated($notification));
            return response()->json([
                'success' => true,
                'message' => 'Test notification created and broadcasted successfully',
                'notification_id' => $notification->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification created but broadcasting failed: ' . $e->getMessage(),
                'notification_id' => $notification->id
            ]);
        }
    }
}
