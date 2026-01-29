@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb :items="[
        ['label' => 'Admin', 'url' => route('admin.dashboard'), 'icon' => 'fas fa-cogs'],
        ['label' => 'Dashboard']
    ]" />

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="mb-1">
                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                </h2>
                <p class="text-muted mb-0">
                    Welcome back! Here's an overview of your issue management system.
                    <span class="live-indicator ms-2" title="Real-time updates active"></span>
                    <small class="text-success">Live</small>
                </p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-ticket-alt me-1"></i><span class="d-none d-md-inline">View Tickets</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-success">
                        <i class="fas fa-users me-1"></i><span class="d-none d-md-inline">Manage Users</span>
                    </a>
                    <form action="{{ route('admin.cleanup-cancelled-tickets') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Are you sure you want to manually trigger the cleanup of cancelled tickets?')">
                            <i class="fas fa-broom me-1"></i><span class="d-none d-lg-inline">Cleanup</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stats-card bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 small text-uppercase">Total Tickets</p>
                            <h3 class="text-white mb-0 fw-bold">{{ $totalTickets }}</h3>
                        </div>
                        <div class="stats-icon bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-ticket-alt text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">
                            <i class="fas fa-chart-line me-1"></i>All time submissions
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stats-card bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 small text-uppercase">Open</p>
                            <h3 class="text-white mb-0 fw-bold">{{ $openTickets ?? 0 }}</h3>
                        </div>
                        <div class="stats-icon bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-inbox text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">
                            <i class="fas fa-clock me-1"></i>Awaiting action
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stats-card bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-dark mb-1 small text-uppercase" style="opacity: 0.7;">In Progress</p>
                            <h3 class="text-dark mb-0 fw-bold">{{ $pendingTickets }}</h3>
                        </div>
                        <div class="stats-icon bg-dark bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-spinner text-dark"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-dark" style="opacity: 0.7;">
                            <i class="fas fa-tools me-1"></i>Being worked on
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stats-card bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 small text-uppercase">Completed</p>
                            <h3 class="text-white mb-0 fw-bold">{{ $completedTickets }}</h3>
                        </div>
                        <div class="stats-icon bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">
                            <i class="fas fa-trophy me-1"></i>Successfully resolved
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    @php
        $total = $totalTickets > 0 ? $totalTickets : 1;
        $openPercent = round((($openTickets ?? 0) / $total) * 100);
        $inProgressPercent = round(($pendingTickets / $total) * 100);
        $completedPercent = round(($completedTickets / $total) * 100);
    @endphp
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-semibold">Ticket Progress Overview</h6>
                <span class="badge bg-primary">{{ $totalTickets }} Total</span>
            </div>
            <div class="progress" style="height: 24px; border-radius: 12px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $openPercent }}%;" title="Open: {{ $openTickets ?? 0 }}">
                    @if($openPercent > 10) Open @endif
                </div>
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $inProgressPercent }}%;" title="In Progress: {{ $pendingTickets }}">
                    @if($inProgressPercent > 10) In Progress @endif
                </div>
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completedPercent }}%;" title="Completed: {{ $completedTickets }}">
                    @if($completedPercent > 10) Completed @endif
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted"><span class="badge bg-info me-1">{{ $openPercent }}%</span> Open</small>
                <small class="text-muted"><span class="badge bg-warning text-dark me-1">{{ $inProgressPercent }}%</span> In Progress</small>
                <small class="text-muted"><span class="badge bg-success me-1">{{ $completedPercent }}%</span> Completed</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Department Statistics -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Department Statistics</h5>
                    <span class="badge bg-light text-dark">{{ $departments->count() }} Departments</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Department</th>
                                    <th class="text-center">Tickets</th>
                                    <th class="text-center" style="width: 150px;">Distribution</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                @php
                                    $deptPercent = $totalTickets > 0 ? round(($department->tickets_count / $totalTickets) * 100) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="dept-icon bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="fas fa-building text-primary" style="width: 16px;"></i>
                                            </div>
                                            <span class="fw-medium">{{ $department->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3">{{ $department->tickets_count }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $deptPercent }}%;"></div>
                                        </div>
                                        <small class="text-muted">{{ $deptPercent }}%</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('tickets.index') }}?department={{ $department->id }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="col-lg-4 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-user-plus me-2"></i>Create User</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-info d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-users me-2"></i>Manage Users</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-plus-circle me-2"></i>Create Ticket</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('test.realtime') }}" class="btn btn-outline-warning d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-broadcast-tower me-2"></i>Test Real-Time</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-server me-2"></i>System Status</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span><i class="fas fa-database text-primary me-2"></i>Database</span>
                            <span class="badge bg-success">Connected</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span><i class="fas fa-broadcast-tower text-info me-2"></i>Real-time</span>
                            <span class="badge bg-success">Active</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span><i class="fas fa-users text-warning me-2"></i>Users</span>
                            <span class="badge bg-primary">{{ \App\Models\User::count() }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <span><i class="fas fa-folder text-secondary me-2"></i>Categories</span>
                            <span class="badge bg-primary">{{ \App\Models\Category::count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stats-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stats-icon i {
        font-size: 1.25rem;
    }

    .dept-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .progress {
        overflow: visible;
    }

    .stats-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    @media (max-width: 767.98px) {
        .stats-card .card-body {
            padding: 1rem;
        }

        .stats-icon {
            width: 40px;
            height: 40px;
        }

        .stats-icon i {
            font-size: 1rem;
        }

        .stats-card h3 {
            font-size: 1.5rem;
        }

        .stats-card .mt-3 {
            display: none;
        }
    }
</style>
@endpush
@endsection
