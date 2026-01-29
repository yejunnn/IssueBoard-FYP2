<?php

namespace App\Http\Controllers;

use App\Events\TicketAccepted;
use App\Events\TicketCreated;
use App\Events\TicketStatusUpdated;
use App\Models\Category;
use App\Models\Ticket;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($request->ajax()) {
            return $this->handleAjaxRequest($request, $user);
        }
        
        $query = Ticket::with(['category', 'department']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('my_tickets') && $user) {
            $query->where('created_by', $user->id);
        }
        
        if ($user && $user->isAdmin()) {
            $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        } elseif ($user) {
            $tickets = $query->where('department_id', $user->department_id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        }
        
        $stats = [
            'total' => $tickets->total(),
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'completed' => $tickets->where('status', 'completed')->count(),
        ];
        
        return view('tickets.index', compact('tickets', 'stats'));
    }
    
    /**
     * Handle AJAX requests for real-time updates
     */
    private function handleAjaxRequest(Request $request, $user)
    {
        $lastUpdate = $request->get('last_update');
        $lastUpdateTime = $lastUpdate ? \Carbon\Carbon::parse($lastUpdate) : \Carbon\Carbon::now()->subMinutes(5);
        
        $query = Ticket::with(['category', 'department', 'acceptedBy', 'acknowledgments']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('my_tickets') && $user) {
            $query->where('created_by', $user->id);
        }
        
        if ($user && $user->isAdmin()) {
        } elseif ($user) {
            $query->where('department_id', $user->department_id);
        }
        
        $updatedTickets = $query->where(function($q) use ($lastUpdateTime) {
            $q->where('updated_at', '>', $lastUpdateTime)
              ->orWhere('created_at', '>', $lastUpdateTime);
        })->orderBy('created_at', 'desc')->get();
        
        $newTickets = $query->where('created_at', '>', $lastUpdateTime)->count();
        
        $updatedTicketsCount = $query->where('updated_at', '>', $lastUpdateTime)
            ->where('created_at', '<=', $lastUpdateTime)
            ->count();
        
        $formattedTickets = $updatedTickets->map(function($ticket) {
            return [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'description' => $ticket->description,
                'location' => $ticket->location,
                'status' => $ticket->status,
                'image_path' => $ticket->image_path,
                'created_at' => $ticket->created_at->toISOString(),
                'updated_at' => $ticket->updated_at->toISOString(),
                'category_name' => $ticket->category->name ?? 'Unknown',
                'department_name' => $ticket->department->name ?? 'Unknown',
                'accepted_by' => $ticket->accepted_by,
                'accepted_by_name' => $ticket->acceptedBy->name ?? null,
                'acknowledgments_count' => $ticket->acknowledgments->count(),
                'acknowledged_by' => $ticket->acknowledgments->count() > 0,
            ];
        });
        
        return response()->json([
            'has_updates' => $updatedTickets->count() > 0,
            'tickets' => $formattedTickets,
            'new_tickets_count' => $newTickets,
            'updated_tickets_count' => $updatedTicketsCount,
            'last_update' => \Carbon\Carbon::now()->toISOString(),
        ]);
    }

    public function assigned(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if (!$user->department_id || $user->is_admin) {
            return redirect()->route('tickets.index')
                ->with('error', 'You do not have permission to view assigned tickets.');
        }
        
        $query = Ticket::with(['category', 'department'])
            ->where('accepted_by', $user->id);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => $tickets->total(),
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'completed' => $tickets->where('status', 'completed')->count(),
        ];
        
        return view('tickets.assigned', compact('tickets', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::with('department')->get();
        return view('tickets.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $category = Category::findOrFail($request->category_id);
        
        $ticketData = [
            'name' => strip_tags($request->name),
            'description' => strip_tags($request->description),
            'category_id' => $request->category_id,
            'location' => strip_tags($request->location),
            'department_id' => $category->department_id,
            'status' => 'open',
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $validation = \App\Helpers\CommonHelper::validateAndSanitizeFile($file);
            
            if (!$validation['valid']) {
                return redirect()->back()
                    ->withErrors(['image' => implode(' ', $validation['errors'])])
                    ->withInput();
            }
            
            $imagePath = $file->store('ticket-images', 'public');
            $ticketData['image_path'] = $imagePath;
        }

        $ticket = Ticket::create($ticketData);

        if (auth()->check()) {
            NotificationService::newTicketCreated($ticket);
            
            try {
                event(new TicketCreated($ticket));
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for ticket creation: ' . $e->getMessage());
            }
        }

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::with(['category', 'department', 'acknowledgedBy'])
            ->findOrFail($id);
        
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $categories = Category::with('department')->get();
        
        return view('tickets.edit', compact('ticket', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'status' => 'required|in:in_progress,completed,cancel',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $category = Category::findOrFail($request->category_id);
        
        $ticketData = [
            'name' => strip_tags($request->name),
            'description' => strip_tags($request->description),
            'category_id' => $request->category_id,
            'location' => strip_tags($request->location),
            'department_id' => $category->department_id,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $validation = \App\Helpers\CommonHelper::validateAndSanitizeFile($file);
            
            if (!$validation['valid']) {
                return redirect()->back()
                    ->withErrors(['image' => implode(' ', $validation['errors'])])
                    ->withInput();
            }
            
            if ($ticket->image_path) {
                Storage::disk('public')->delete($ticket->image_path);
            }
            
            $imagePath = $file->store('ticket-images', 'public');
            $ticketData['image_path'] = $imagePath;
        }

        $ticket->update($ticketData);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        if ($ticket->image_path) {
            Storage::disk('public')->delete($ticket->image_path);
        }
        
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    /**
     * Acknowledge a completed ticket.
     */
    public function acknowledge(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        
        if ($ticket->status !== 'completed') {
            if (request()->ajax()) {
                return response()->json(['error' => 'Only completed tickets can be acknowledged.'], 400);
            }
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Only completed tickets can be acknowledged.');
        }
        
        if ($user) {
            if ($user->department_id && !$user->is_admin) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'Department users cannot acknowledge tickets.'], 403);
                }
                return redirect()->route('tickets.show', $ticket)
                    ->with('error', 'Department users cannot acknowledge tickets.');
            }
            
            if ($ticket->hasBeenAcknowledgedBy($user)) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'You have already acknowledged this ticket.'], 400);
                }
                return redirect()->route('tickets.show', $ticket)
                    ->with('error', 'You have already acknowledged this ticket.');
            }
            
            $acknowledgment = $ticket->acknowledgments()->create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'comment' => strip_tags(request('comment'))
            ]);
            
            NotificationService::ticketAcknowledged($ticket, $user);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket acknowledged successfully!',
                    'acknowledgment' => [
                        'id' => $acknowledgment->id,
                        'user_name' => $user->name,
                        'created_at' => $acknowledgment->created_at->toISOString()
                    ],
                    'ticket' => [
                        'id' => $ticket->id,
                        'acknowledgments_count' => $ticket->acknowledgments->count()
                    ]
                ]);
            }
            
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket acknowledged successfully!');
        } else {
            $anonymousIdentifier = 'anon_' . md5(request()->ip() . request()->userAgent() . $ticket->id);
            
            if ($ticket->hasBeenAcknowledgedBy(null, $anonymousIdentifier)) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'You have already acknowledged this ticket.'], 400);
                }
                return redirect()->route('tickets.show', $ticket)
                    ->with('error', 'You have already acknowledged this ticket.');
            }
            
            $acknowledgment = $ticket->acknowledgments()->create([
                'anonymous_identifier' => $anonymousIdentifier,
                'ip_address' => request()->ip(),
                'comment' => strip_tags(request('comment'))
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket acknowledged successfully!',
                    'acknowledgment' => [
                        'id' => $acknowledgment->id,
                        'user_name' => 'Anonymous User',
                        'created_at' => $acknowledgment->created_at->toISOString()
                    ],
                    'ticket' => [
                        'id' => $ticket->id,
                        'acknowledgments_count' => $ticket->acknowledgments->count()
                    ]
                ]);
            }
            
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket acknowledged successfully!');
        }
    }



    public function accept(string $ticket_id)
    {
        $ticket = Ticket::findOrFail($ticket_id);
        $user = auth()->user();
        
        if (!$user || !$user->department_id || $ticket->department_id !== $user->department_id) {
            return redirect()->back()->with('error', 'You can only accept tickets assigned to your department.');
        }
        
        if ($ticket->status !== 'open') {
            return redirect()->back()->with('error', 'You can only accept tickets that are in open status.');
        }
        
        if ($ticket->accepted_by) {
            return redirect()->back()->with('error', 'This ticket has already been accepted.');
        }
        
        $ticket->update([
            'accepted_by' => $user->id,
            'status' => 'in_progress',
        ]);
        
        NotificationService::ticketAccepted($ticket, $user);
        
        try {
            event(new TicketAccepted($ticket, $user));
        } catch (\Exception $e) {
            \Log::error('Broadcasting failed for ticket acceptance: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'Ticket accepted successfully! Status changed to In Progress.');
    }

    /**
     * Update ticket status (for department users)
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && $ticket->department_id !== $user->department_id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'You can only update tickets assigned to your department.'], 403);
            }
            return redirect()->back()->with('error', 'You can only update tickets assigned to your department.');
        }
        
        $request->validate([
            'status' => 'required|in:open,in_progress,completed,cancel'
        ]);
        
        $oldStatus = $ticket->status;
        $ticket->update(['status' => $request->status]);
        
        NotificationService::ticketStatusUpdated($ticket, $user, $oldStatus, $request->status);
        
        try {
            event(new TicketStatusUpdated($ticket, $oldStatus, $request->status));
        } catch (\Exception $e) {
            \Log::error('Broadcasting failed for status update: ' . $e->getMessage());
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully!',
                'ticket' => [
                    'id' => $ticket->id,
                    'status' => $ticket->status,
                    'status_text' => ucfirst(str_replace('_', ' ', $ticket->status))
                ]
            ]);
        }
        
        return redirect()->back()->with('success', 'Ticket status updated successfully!');
    }

    public function cancel(Ticket $ticket)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'You do not have permission to cancel tickets.'], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to cancel tickets.');
        }
        
        $oldStatus = $ticket->status;
        $ticket->update(['status' => 'cancel']);
        
        NotificationService::ticketStatusUpdated($ticket, $user, $oldStatus, 'cancel');
        
        try {
            event(new TicketStatusUpdated($ticket, $oldStatus, 'cancel'));
        } catch (\Exception $e) {
            \Log::error('Broadcasting failed for ticket cancellation: ' . $e->getMessage());
        }
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket cancelled successfully!',
                'ticket' => [
                    'id' => $ticket->id,
                    'status' => $ticket->status,
                    'status_text' => 'Cancel'
                ]
            ]);
        }
        
        return redirect()->back()->with('success', 'Ticket cancelled successfully!');
    }

    public function restore(Ticket $ticket)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'You do not have permission to restore tickets.'], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to restore tickets.');
        }
        
        if ($ticket->status !== 'cancel') {
            if (request()->ajax()) {
                return response()->json(['error' => 'Only cancelled tickets can be restored.'], 400);
            }
            return redirect()->back()->with('error', 'Only cancelled tickets can be restored.');
        }
        
        $oldStatus = $ticket->status;
        $ticket->update(['status' => 'open']);
        
        NotificationService::ticketStatusUpdated($ticket, $user, $oldStatus, 'open');
        
        try {
            event(new TicketStatusUpdated($ticket, $oldStatus, 'open'));
        } catch (\Exception $e) {
            \Log::error('Broadcasting failed for ticket restoration: ' . $e->getMessage());
        }
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket restored successfully!',
                'ticket' => [
                    'id' => $ticket->id,
                    'status' => $ticket->status,
                    'status_text' => 'Open'
                ]
            ]);
        }
        
        return redirect()->back()->with('success', 'Ticket restored successfully!');
    }
}
