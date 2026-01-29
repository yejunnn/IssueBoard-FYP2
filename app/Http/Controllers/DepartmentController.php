<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * Show the department dashboard with tickets assigned to the department.
     */
    public function dashboard()
    {
        return redirect()->route('tickets.index');
    }
} 