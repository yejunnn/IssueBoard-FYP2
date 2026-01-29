<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        if (!$user->department_id) {
            abort(403, 'Access denied. Only department users can view profiles.');
        }
        
        $ticketStats = [
            'accepted' => Ticket::where('accepted_by', $user->id)->count(),
            'in_progress' => Ticket::where('accepted_by', $user->id)->where('status', 'in_progress')->count(),
            'completed' => Ticket::where('accepted_by', $user->id)->where('status', 'completed')->count(),
            'total' => Ticket::where('accepted_by', $user->id)->count(),
        ];
        
        return view('profile.show', compact('user', 'ticketStats'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->department_id) {
            abort(403, 'Access denied. Only department users can update profiles.');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        
        $user->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Profile information updated successfully.');
    }
    
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->department_id) {
            abort(403, 'Access denied. Only department users can update passwords.');
        }
        
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}
