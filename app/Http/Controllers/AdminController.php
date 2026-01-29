<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\Ticket;
use App\Events\TicketAssigned;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $pendingTickets = Ticket::where('status', 'in_progress')->count();
        $completedTickets = Ticket::where('status', 'completed')->count();

        $departments = Department::withCount('tickets')->get();

        return view('admin.dashboard', compact('totalTickets', 'openTickets', 'pendingTickets', 'completedTickets', 'departments'));
    }

    /**
     * Show user management page
     */
    public function users()
    {
        $users = User::with('department')->orderBy('created_at', 'desc')->get();
        $departments = Department::all();
        
        return view('admin.users', compact('users', 'departments'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        $departments = Department::all();
        return view('admin.create-user', compact('departments'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        $departments = Department::all();
        return view('admin.edit-user', compact('user', 'departments'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'is_admin' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account!');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    public function cleanupCancelledTickets()
    {
        try {
            \Artisan::call('tickets:delete-cancelled');
            $output = \Artisan::output();
            
            return redirect()->back()->with('success', 'Cancelled tickets cleanup completed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error during cleanup: ' . $e->getMessage());
        }
    }

    public function showAssignTicket(Ticket $ticket)
    {
        $departmentUsers = User::whereNotNull('department_id')
            ->where('is_admin', false)
            ->orderByRaw("CASE WHEN department_id = ? THEN 0 ELSE 1 END", [$ticket->department_id])
            ->orderBy('department_id')
            ->orderBy('name')
            ->get();
        
        return view('admin.assign-ticket', compact('ticket', 'departmentUsers'));
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        try {
            \Log::info('Ticket assignment attempt', [
                'ticket_id' => $ticket->id,
                'request_data' => $request->all(),
                'user' => auth()->user()->name ?? 'Unknown'
            ]);

            try {
                $request->validate([
                    'assigned_user_id' => 'required|exists:users,id',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Validation failed for ticket assignment', [
                    'errors' => $e->errors(),
                    'ticket_id' => $ticket->id
                ]);
                return redirect()->back()->withErrors($e->errors())->withInput();
            }

            $assignedUser = User::findOrFail($request->assigned_user_id);
            
            \Log::info('Found assigned user', [
                'assigned_user_id' => $assignedUser->id,
                'assigned_user_name' => $assignedUser->name
            ]);
            
            $ticket->update([
                'accepted_by' => $request->assigned_user_id,
                'status' => 'in_progress'
            ]);

            \Log::info('Ticket updated successfully', [
                'ticket_id' => $ticket->id,
                'new_accepted_by' => $ticket->accepted_by,
                'new_status' => $ticket->status
            ]);

            NotificationService::ticketAssigned($ticket, $assignedUser);

            try {
                event(new TicketAssigned($ticket, $assignedUser, auth()->user()));
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for ticket assignment: ' . $e->getMessage());
            }

            $message = "Ticket #{$ticket->id} assigned to {$assignedUser->name}";
            
            return redirect()->route('tickets.index')->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during ticket assignment', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'An error occurred while assigning the ticket. Please try again.');
        }
    }

    public function statistics()
    {
        if (!request()->ajax()) {
            abort(404);
        }

        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'completed' => Ticket::where('status', 'completed')->count(),
            'cancel' => Ticket::where('status', 'cancel')->count(),
        ];

        return response()->json($stats);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,completed',
            'ticket_ids' => 'required|json'
        ]);

        $ticketIds = json_decode($request->ticket_ids, true);
        $status = $request->status;
        $user = auth()->user();

        $updatedCount = 0;
        foreach ($ticketIds as $ticketId) {
            $ticket = Ticket::find($ticketId);
            if ($ticket) {
                $oldStatus = $ticket->status;
                $ticket->update(['status' => $status]);
                
                NotificationService::ticketStatusUpdated($ticket, $user, $oldStatus, $status);
                $updatedCount++;
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully updated status for {$updatedCount} ticket(s).",
                'updated_count' => $updatedCount,
                'status' => $status
            ]);
        }

        return redirect()->back()->with('success', "Successfully updated status for {$updatedCount} ticket(s).");
    }

    public function bulkCancelTickets(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|json'
        ]);

        $ticketIds = json_decode($request->ticket_ids, true);
        $user = auth()->user();

        $cancelledCount = 0;
        foreach ($ticketIds as $ticketId) {
            $ticket = Ticket::find($ticketId);
            if ($ticket && $ticket->status !== 'cancel') {
                $oldStatus = $ticket->status;
                $ticket->update(['status' => 'cancel']);
                
                NotificationService::ticketStatusUpdated($ticket, $user, $oldStatus, 'cancel');
                $cancelledCount++;
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully cancelled {$cancelledCount} ticket(s).",
                'cancelled_count' => $cancelledCount
            ]);
        }

        return redirect()->back()->with('success', "Successfully cancelled {$cancelledCount} ticket(s).");
    }

    protected static function middleware()
    {
        return [
            'auth',
            'admin',
        ];
    }
}
