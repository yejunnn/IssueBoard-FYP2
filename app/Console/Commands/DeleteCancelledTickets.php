<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteCancelledTickets extends Command
{
    protected $signature = 'tickets:delete-cancelled {--days=7 : Number of days to wait before deletion}';

    protected $description = 'Delete cancelled tickets that are older than specified days (default: 7 days)';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Deleting cancelled tickets older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')})...");
        
        $cancelledTickets = Ticket::where('status', 'cancel')
            ->where('updated_at', '<', $cutoffDate)
            ->get();
        
        $deletedCount = 0;
        $imageDeletedCount = 0;
        
        foreach ($cancelledTickets as $ticket) {
            if ($ticket->image_path) {
                try {
                    Storage::disk('public')->delete($ticket->image_path);
                    $imageDeletedCount++;
                    $this->line("Deleted image: {$ticket->image_path}");
                } catch (\Exception $e) {
                    $this->warn("Failed to delete image {$ticket->image_path}: {$e->getMessage()}");
                }
            }
            
            $ticket->delete();
            $deletedCount++;
            
            $this->line("Deleted ticket ID {$ticket->id}: {$ticket->name}");
        }
        
        $this->info("Completed! Deleted {$deletedCount} cancelled tickets and {$imageDeletedCount} associated images.");
        
        return Command::SUCCESS;
    }
}
