<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow p-5">
                    <h1 class="display-4 text-danger mb-3"><i class="fas fa-ban me-2"></i>403</h1>
                    <h2 class="mb-4">Access Denied</h2>
                    <p class="lead mb-4">You do not have permission to access this page.</p>
                    @php $user = Auth::user(); @endphp
                    @if($user && $user->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">Go to Admin Dashboard</a>
                    @elseif($user && $user->department_id)
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Go to Dashboard</a>
                    @else
                        <a href="/" class="btn btn-primary btn-lg">Go to Home</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</body>
</html> 